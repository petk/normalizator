<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\Finder\File;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\Slugify;

/**
 * Normalizator to check and fix names of files and directories.
 *
 * Most strictly only ASCII characters are allowed without spaces and such.
 */
#[Normalization(
    name: 'path-name',
    filters: [
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class PathNameNormalization extends AbstractNormalization
{
    public function __construct(
        private Slugify $slugify,
        private FilenameResolver $filenameResolver
    ) {
    }

    /**
     * Normalizes name of the given path.
     */
    public function normalize(File $file): File
    {
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

        // Check if file with such new filename already exists and resolve it.
        $newFilename = $this->filenameResolver->resolve($file, $newFilename);

        if ($newFilename !== $file->getNewFilename()) {
            $file->setNewFilename($newFilename);
            $this->notify($file, 'path rename ' . $newFilename);
        }

        return $file;
    }
}
