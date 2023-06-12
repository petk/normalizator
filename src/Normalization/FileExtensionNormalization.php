<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\Finder\File;
use Normalizator\Util\FilenameResolver;

/**
 * Utility to normalize file extensions.
 */
#[Normalization(
    name: 'extension',
    filters: [
        'file',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class FileExtensionNormalization extends AbstractNormalization
{
    /**
     * These filenames are known and shouldn't be normalized.
     *
     * @var array<int,string>
     */
    private array $knownFiles = [
        // .DS_Store file on macOS stores containing folder's attributes.
        '.DS_Store',
    ];

    /**
     * @var array<string,string>
     */
    private array $extensions = [
        'jpeg' => 'jpg',
    ];

    public function __construct(private FilenameResolver $filenameResolver)
    {
    }

    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        // If the filename is known leave it as it is.
        if (in_array($file->getFilename(), $this->knownFiles, true)) {
            return $file;
        }

        $extension = $file->getExtension();

        // Files without extensions shouldn't have extension normalized.
        if ('' === $extension) {
            return $file;
        }

        $newExtension = strtolower($extension);

        // Trim special characters from extension.
        $newExtension = trim($newExtension, ' -');

        if (isset($this->extensions[$newExtension])) {
            $newExtension = $this->extensions[$newExtension];
        }

        $basename = rtrim($file->getFilename(), $extension) . $newExtension;

        // Check if file with such new filename already exists and resolve it.
        $basename = $this->filenameResolver->resolve($file, $basename);

        if ($basename !== $file->getFilename()) {
            $file->setNewFilename($basename);
            $this->notify('file extension');
        }

        return $file;
    }
}
