<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;
use Normalizator\Util\EolDiscovery;

use function is_array;
use function is_bool;
use function is_string;
use function Normalizator\preg_match_all;
use function Normalizator\preg_replace;

/**
 * Utility to convert EOL characters to same style.
 */
#[Normalization(
    name: 'eol',
    filters: [
        'file',
        'plain_text',
        'no_git',
        'no_node_modules',
        'no_svn',
        'no_vendor',
    ]
)]
class EolNormalization implements NormalizationInterface, ConfigurableNormalizationInterface
{
    /**
     * Some test files might include also CR characters as part of the test so
     * those can be skipped.
     */
    public const SKIP_CR = false;

    private string $eol = EolDiscovery::DEFAULT_EOL;
    private bool $skipCr = self::SKIP_CR;

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

        if (isset($options['skip_cr']) && is_bool($options['skip_cr'])) {
            $this->skipCr = $options['skip_cr'];
        } else {
            $this->skipCr = self::SKIP_CR;
        }
    }

    /**
     * Convert all EOL characters to default EOL.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        if ($this->skipCr) {
            $regex = '/(?>\r\n|\n)/m';
        } else {
            $regex = '/(*BSR_ANYCRLF)\R/m';
        }

        $defaultEol = $this->eolDiscovery->getEolForFile($file, $this->eol);

        $content = $file->getNewContent();
        $newContent = preg_replace($regex, $defaultEol, $content);

        if (!is_array($newContent) && $content !== $newContent) {
            $file->setNewContent($newContent);
            // Report EOLs from the original content.
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, $this->getEols($file->getContent()) . 'line terminators'));
        }

        return $file;
    }

    /**
     * Get EOL sequence for the given content.
     */
    private function getEols(string $content): string
    {
        $eols = ['lf' => 0, 'crlf' => 0, 'cr' => 0];

        // Match all LF, CRLF and CR EOL characters
        preg_match_all('/(*BSR_ANYCRLF)\R/', $content, $matches);

        foreach ($matches[0] as $match) {
            if ("\n" === $match) {
                ++$eols['lf'];
            } elseif ("\r\n" === $match) {
                ++$eols['crlf'];
            } elseif ("\r" === $match) {
                ++$eols['cr'];
            }
        }

        $message = ($eols['lf'] > 0) ? $eols['lf'] . ' LF ' : '';
        $message .= ($eols['crlf'] > 0) ? $eols['crlf'] . ' CRLF ' : '';
        $message .= ($eols['cr'] > 0) ? $eols['cr'] . ' CR ' : '';

        return $message;
    }
}
