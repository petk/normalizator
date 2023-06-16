<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Finder\File;

use function Normalizator\preg_match;
use function Normalizator\preg_replace;

/**
 * Utility to clean all spaces before tab in the initial indent part of the
 * lines.
 */
#[Normalization(
    name: 'space-before-tab',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class SpaceBeforeTabNormalization extends AbstractNormalization
{
    public function __construct(private EventDispatcher $eventDispatcher)
    {
    }

    /**
     * Clean all spaces in front of the tabs in the initial indent part of the
     * lines.
     */
    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        $regex = '/^(\t*)([ ]+)(\t+)/m';
        $content = $newContent = $file->getNewContent();

        while (!is_array($newContent) && 1 === preg_match($regex, $newContent)) {
            $newContent = preg_replace($regex, '$1$3', $newContent);
        }

        if (!is_array($newContent) && $content !== $newContent) {
            $file->setNewContent($newContent);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'space before tab'));
        }

        return $file;
    }
}
