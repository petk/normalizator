<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\Configuration\Configuration;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;

use function Normalizator\preg_match;
use function Normalizator\preg_split;

/**
 * The middle newlines trimming utility.
 */
#[Normalization(
    name: 'middle-eol',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class MiddleEolNormalization implements NormalizationInterface, ConfigurableNormalizationInterface
{
    /**
     * Number of allowed redundant middle EOLs.
     */
    public const MAX_EXTRA_MIDDLE_EOLS = 1;

    /**
     * Overridden configuration values.
     *
     * @var array<string,mixed>
     */
    private array $overrides;

    public function __construct(
        private Configuration $configuration,
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function configure(array $values): void
    {
        $this->overrides = $values;
    }

    /**
     * Trim all redundant newlines from the middle of the file.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $content = $file->getNewContent();

        // Get beginninig.
        preg_match('/(*BSR_ANYCRLF)^\R*\s*/', $content, $matches);
        $beginning = $matches[0] ?? '';

        // Get end.
        preg_match('/(*BSR_ANYCRLF)\s*\R*$/', $content, $matches);
        $end = $matches[0] ?? '';

        $lines = preg_split('/(*BSR_ANYCRLF)(\R)/', trim($content), -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);

        $trimmed = '';
        $redundantCount = 0;

        foreach ($lines as $line) {
            if ('' === trim($line)) {
                ++$redundantCount;
            } else {
                $redundantCount = 0;
            }

            if ($redundantCount > $this->getConfig('max_extra_middle_eols', self::MAX_EXTRA_MIDDLE_EOLS) + 1) {
                $line = '';
            }

            $trimmed .= $line;
        }

        $newContent = $beginning . $trimmed . $end;

        if ($content !== $newContent) {
            $file->setNewContent($newContent);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'redundant middle EOL(s)'));
        }

        return $file;
    }

    private function getConfig(string $key, mixed $default = null): mixed
    {
        if (isset($this->overrides[$key])) {
            return $this->overrides[$key];
        }

        return $this->configuration->get($key, $default);
    }
}
