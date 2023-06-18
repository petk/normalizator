<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;

/**
 * The leading newlines trimming utility.
 */
#[Normalization(
    name: 'leading-eol',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class LeadingEolNormalization implements NormalizationInterface
{
    public function __construct(
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher
    ) {
    }

    /**
     * Trim all newlines from the beginning of the file.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $content = $file->getNewContent();

        $newContent = ltrim($content, "\r\n");

        if ($content !== $newContent) {
            $file->setNewContent($newContent);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'leading EOL(s)'));
        }

        return $file;
    }
}
