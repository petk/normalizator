<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;
use Normalizator\Util\Slugify;

/**
 * Normalizator to check and fix names of files and directories.
 *
 * Most strictly only ASCII characters are allowed without spaces and such.
 */
#[Normalization(
    name: 'name',
    filters: [
        'no_git',
        'no_node_modules',
        'no_svn',
        'no_vendor',
    ]
)]
class NameNormalization implements NormalizationInterface
{
    public function __construct(
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher,
        private Slugify $slugify,
    ) {
    }

    /**
     * Normalizes name of the given path.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $extension = $file->getExtension();
        $extension = ('' !== $extension) ? '.' . $extension : '';
        $nameWithoutExtension = $file->getNewFilename();

        if (
            '' !== $extension
            && false !== $position = strrpos($file->getNewFilename(), '.')
        ) {
            $nameWithoutExtension = substr($file->getNewFilename(), 0, $position);
        }

        $nameWithoutExtension = $this->slugify->slugify($nameWithoutExtension);

        $newFilename = $nameWithoutExtension . $extension;

        if ($newFilename !== $file->getNewFilename()) {
            $file->setNewFilename($newFilename);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'path rename ' . $newFilename));
        }

        return $file;
    }
}
