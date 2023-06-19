<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\Configuration\Configuration;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;
use Normalizator\Util\EolDiscovery;

/**
 * Normalizes final newlines.
 */
#[Normalization(
    name: 'final-eol',
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

    /**
     * Overridden configuration values.
     *
     * @var array<string,mixed>
     */
    private array $overrides;

    /**
     * Class constructor.
     */
    public function __construct(
        private Configuration $configuration,
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher,
        private EolDiscovery $eolDiscovery
    ) {
    }

    public function configure(array $values): void
    {
        $this->overrides = $values;
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

        $max = $this->getConfig('max_extra_final_eols', self::MAX_EXTRA_FINAL_EOLS);

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
        if ('' !== $trimmed && !in_array(substr($trimmed, -1), ["\n", "\r"], true)) {
            $trimmed .= $this->eolDiscovery->getPrevailingEol($content);
        }

        if ($content !== $trimmed) {
            $file->setNewContent($trimmed);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, count($newlines) . ' final EOL(s)'));
        }

        return $file;
    }

    private function getConfig(string $key, mixed $default): mixed
    {
        if (isset($this->overrides[$key])) {
            return $this->overrides[$key];
        }

        return $this->configuration->get($key, $default);
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
}
