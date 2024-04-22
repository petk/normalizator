<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;

use function in_array;
use function rtrim;
use function strtolower;
use function trim;

/**
 * Utility to normalize file extensions.
 */
#[Normalization(
    name: 'extension',
    filters: [
        'file',
        'no_git',
        'no_node_modules',
        'no_svn',
        'no_vendor',
    ],
)]
class ExtensionNormalization implements NormalizationInterface
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
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher,
    ) {}

    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
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
