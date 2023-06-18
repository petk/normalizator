<?php

declare(strict_types=1);

namespace Normalizator;

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
    public const VERSION = '0.0.2-dev';

    /**
     * @var array<string,null|array<mixed>|bool|float|int|string>
     */
    private array $options = [];

    public function __construct(
        private NormalizationFactory $normalizationFactory,
        private FilenameResolver $filenameResolver,
        private EventDispatcher $eventDispatcher,
        private Logger $logger,
    ) {
    }

    /**
     * Set options for normalization configuration.
     *
     * @param array<string,null|array<mixed>|bool|float|int|string> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
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

        if (false !== $this->options['encoding']) {
            $path = $this->normalizationFactory->make('encoding')->normalize($path);
        }

        if (false !== $this->options['trailing-whitespace']) {
            $path = $this->normalizationFactory->make('trailing-whitespace')->normalize($path);
        }

        if (false !== $this->options['final-eol']) {
            $path = $this->normalizationFactory->make('final-eol', ['max' => (int) ($this->options['final-eol'] ?? 1)])->normalize($path);
        }

        if (false !== $this->options['eol']) {
            $path = $this->normalizationFactory->make('eol')->normalize($path);
        }

        if (false !== $this->options['leading-eol']) {
            $path = $this->normalizationFactory->make('leading-eol')->normalize($path);
        }

        if (false !== $this->options['middle-eol']) {
            $path = $this->normalizationFactory->make('middle-eol', ['max' => (int) ($this->options['middle-eol'] ?? 1)])->normalize($path);
        }

        if (false !== $this->options['space-before-tab']) {
            $path = $this->normalizationFactory->make('space-before-tab')->normalize($path);
        }

        return $path;
    }

    private function normalizePath(File $path): File
    {
        if (false !== $this->options['extension']) {
            $path = $this->normalizationFactory->make('extension')->normalize($path);
        }

        if (false !== $this->options['name']) {
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
        if (!isset($this->options['permissions']) || false === $this->options['permissions']) {
            return $path;
        }

        return $this->normalizationFactory->make('permissions')->normalize($path);
    }
}
