<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Configuration\Configuration;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Finder\File;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\Logger;

/**
 * Normalizes content of given file.
 */
class Normalizator implements NormalizatorInterface
{
    public const VERSION = '0.0.3-dev';

    public function __construct(
        private Configuration $configuration,
        private NormalizationFactory $normalizationFactory,
        private FilenameResolver $filenameResolver,
        private EventDispatcher $eventDispatcher,
        private Logger $logger,
    ) {
    }

    public function normalize(File $path): void
    {
        $this->normalizeContent($path);
        $this->normalizePermissions($path);
        $this->normalizePath($path);
    }

    public function save(File $path): void
    {
        if (!$this->isNormalized($path)) {
            $this->normalize($path);
        }

        $path->save();
    }

    public function isNormalized(File $file): bool
    {
        return [] !== array_merge(
            $this->logger->getLogs($file),
            $this->logger->getErrors($file)
        );
    }

    private function normalizeContent(File $path): File
    {
        if (!$path->isFile()) {
            return $path;
        }

        if (false !== $this->configuration->get('encoding')) {
            $path = $this->normalizationFactory->make('encoding')->normalize($path);
        }

        if (false !== $this->configuration->get('trailing-whitespace')) {
            $path = $this->normalizationFactory->make('trailing-whitespace')->normalize($path);
        }

        if (false !== $this->configuration->get('final-eol')) {
            $path = $this->normalizationFactory->make('final-eol')->normalize($path);
        }

        if (false !== $this->configuration->get('eol')) {
            $path = $this->normalizationFactory->make('eol')->normalize($path);
        }

        if (false !== $this->configuration->get('leading-eol')) {
            $path = $this->normalizationFactory->make('leading-eol')->normalize($path);
        }

        if (false !== $this->configuration->get('middle-eol')) {
            $path = $this->normalizationFactory->make('middle-eol')->normalize($path);
        }

        if (false !== $this->configuration->get('space-before-tab')) {
            $path = $this->normalizationFactory->make('space-before-tab')->normalize($path);
        }

        return $path;
    }

    private function normalizePath(File $path): File
    {
        if (false !== $this->configuration->get('extension')) {
            $path = $this->normalizationFactory->make('extension')->normalize($path);
        }

        if (false !== $this->configuration->get('name')) {
            $path = $this->normalizationFactory->make('name')->normalize($path);
        }

        // Check if file with such new filename already exists and resolve it.
        $previousFilename = $path->getNewFilename();
        $path = $this->filenameResolver->resolve($path);
        if ($previousFilename !== $path->getNewFilename()) {
            $this->eventDispatcher->dispatch(new NormalizationEvent($path, 'file with the same name already exists; new filename: ' . $path->getNewFilename()));
        }

        return $path;
    }

    private function normalizePermissions(File $path): File
    {
        if (false !== $this->configuration->get('permissions')) {
            $path = $this->normalizationFactory->make('permissions')->normalize($path);
        }

        return $path;
    }
}
