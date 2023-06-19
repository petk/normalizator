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

use function Normalizator\preg_match_all;
use function Normalizator\preg_replace;

/**
 * Utility to convert EOL characters to same style.
 */
#[Normalization(
    name: 'eol',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class EolNormalization implements NormalizationInterface, ConfigurableNormalizationInterface
{
    /**
     * Some test files might include also CR characters as part of the test so
     * those can be skipped.
     */
    public const SKIP_CR = false;

    /**
     * Overridden configuration values.
     *
     * @var array<string,mixed>
     */
    private array $overrides;

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
     * Convert all EOL characters to default EOL.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        if ($this->getConfig('skip_cr', self::SKIP_CR)) {
            $regex = '/(?>\r\n|\n)/m';
        } else {
            $regex = '/(*BSR_ANYCRLF)\R/m';
        }

        $defaultEol = $this->eolDiscovery->getDefaultEol($file);
        $content = $file->getNewContent();
        $newContent = preg_replace($regex, $defaultEol, $content);

        if (!is_array($newContent) && $content !== $newContent) {
            $file->setNewContent($newContent);
            // Report EOLs from the original content.
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, $this->getEols($file->getContent()) . 'line terminators'));
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

        $message = ($eols['lf'] > 0) ? $eols['lf'].' LF ' : '';
        $message .= ($eols['crlf'] > 0) ? $eols['crlf'].' CRLF ' : '';
        $message .= ($eols['cr'] > 0) ? $eols['cr'].' CR ' : '';

        return $message;
    }
}
