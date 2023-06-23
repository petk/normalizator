<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;
use Normalizator\Util\EolDiscovery;

use function Normalizator\preg_match_all;

/**
 * Normalizes final newlines.
 */
#[Normalization(
    name: 'final_eol',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class FinalEolNormalization implements NormalizationInterface, ConfigurableNormalizationInterface
{
    /**
     * Number of allowed redundant final EOLs.
     */
    public const MAX_EXTRA_FINAL_EOLS = 1;

    private string $eol = EolDiscovery::DEFAULT_EOL;
    private int $maxExtraFinalEols = self::MAX_EXTRA_FINAL_EOLS;

    /**
     * Class constructor.
     */
    public function __construct(
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher,
        private EolDiscovery $eolDiscovery
    ) {
    }

    public function configure(mixed ...$options): void
    {
        if (isset($options['eol']) && is_string($options['eol'])) {
            $map = ['lf' => "\n", 'crlf' => "\r\n"];
            $this->eol = $map[$options['eol']] ?? EolDiscovery::DEFAULT_EOL;
        } else {
            $this->eol = EolDiscovery::DEFAULT_EOL;
        }

        if (isset($options['max_extra_final_eols']) && is_int($options['max_extra_final_eols'])) {
            $this->maxExtraFinalEols = $options['max_extra_final_eols'];
        } else {
            $this->maxExtraFinalEols = self::MAX_EXTRA_FINAL_EOLS;
        }
    }

    /**
     * Insert one missing final newline at the end of the string, using a
     * prevailing EOL from the given string - LF (\n), CRLF (\r\n) or CR (\r).
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $content = $file->getNewContent();

        $newlines = $this->getFinalEols($content);
        $trimmed = rtrim($content, "\r\n");

        $max = $this->maxExtraFinalEols;

        // Empty content doesn't need one final newline when max = 1
        if ('' === $trimmed && 1 === $max) {
            $max = 0;
        }

        for ($i = 0; $i < $max; ++$i) {
            if (empty($newlines[$i])) {
                break;
            }

            $trimmed .= $newlines[$i];
        }

        // Then insert one missing final EOL if not present yet.
        if (
            '' !== $trimmed
            && !in_array(substr($trimmed, -1), ["\n", "\r"], true)
            && 0 < $max
        ) {
            $trimmed .= $this->getPrevailingEol($content, $file);
        }

        if ($content !== $trimmed) {
            $file->setNewContent($trimmed);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, count($newlines) . ' final EOL(s)'));
        }

        return $file;
    }

    /**
     * Get all final newlines.
     *
     * @return array<int,string>
     */
    private function getFinalEols(string $content): array
    {
        $newlines = [];

        while ('' !== $content) {
            if ("\r\n" === substr($content, -2)) {
                $newlines[] = "\r\n";
                $content = substr($content, 0, -2);

                continue;
            }

            if ("\n" === substr($content, -1)) {
                $newlines[] = "\n";
                $content = substr($content, 0, -1);

                continue;
            }

            if ("\r" === substr($content, -1)) {
                $newlines[] = "\r";
                $content = substr($content, 0, -1);

                continue;
            }

            break;
        }

        return array_reverse($newlines);
    }

    /**
     * Get EOL based on the prevailing LF, CRLF or CR newline characters.
     */
    private function getPrevailingEol(string $content, File $file): string
    {
        // Match all LF, CRLF and CR EOL characters
        preg_match_all('/(*BSR_ANYCRLF)\R/', $content, $matches);

        if (is_array($matches[0]) && [] !== $matches[0]) {
            $counts = array_count_values($matches[0]);
            arsort($counts);

            return (string) key($counts);
        }

        // For single line files the EOL needs to be determined.
        return $this->eolDiscovery->getEolForFile($file, $this->eol);
    }
}
