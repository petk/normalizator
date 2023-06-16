<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Finder\File;

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
class ExtensionNormalization extends AbstractNormalization
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

    public function __construct(
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        // If the filename is known leave it as it is.
        if (in_array($file->getNewFilename(), $this->knownFiles, true)) {
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

        $basename = rtrim($file->getNewFilename(), $extension) . $newExtension;

        if ($basename !== $file->getNewFilename()) {
            $file->setNewFilename($basename);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'file extension: ' . $extension . ' -> ' . $newExtension));
        }

        return $file;
    }
}