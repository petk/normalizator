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
     * These extensions are known and shouldn't be fully normalized.
     *
     * @var array<int,string>
     */
    private array $knownExtensions = [
        // Files with uppercase .S extension include assembly code that needs to
        // go through a pre-processor. The lowercase .s is assembly code that
        // can be compiled into an object.
        'S',
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

        $newExtension = $extension;

        // Trim special characters from extension.
        $newExtension = trim($newExtension, ' -');

        // If the extension after trimming is known, leave it as it is.
        if (in_array($newExtension, $this->knownExtensions, true)) {
            $this->setFile($file, $extension, $newExtension);

            return $file;
        }

        $newExtension = strtolower($newExtension);

        if (isset($this->extensions[$newExtension])) {
            $newExtension = $this->extensions[$newExtension];
        }

        $this->setFile($file, $extension, $newExtension);

        return $file;
    }

    private function setFile(File $file, string $previousExtension, string $newExtension)
    {
        $basename = rtrim($file->getNewFilename(), $previousExtension) . $newExtension;

        if ($basename !== $file->getNewFilename()) {
            $file->setNewFilename($basename);
            $this->eventDispatcher->dispatch(new NormalizationEvent(
                $file,
                'file extension: ' . $previousExtension . ' -> ' . $newExtension,
            ));
        }
    }
}
