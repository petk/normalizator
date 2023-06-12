<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Finder\File;
use Normalizator\Observer\NormalizationObserver;

/**
 * Normalizes content of given file.
 */
class Normalizator implements NormalizatorInterface
{
    public const VERSION = '0.0.1';

    /**
     * @var array<string,null|array<mixed>|bool|float|int|string>
     */
    private array $options = [];

    public function __construct(
        private NormalizationFactory $normalizationFactory,
        private NormalizationObserver $normalizationObserver,
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
        $this->normalizationObserver->clean();

        $this->normalizeContent($path);
        $this->normalizePermissions($path);
        $this->normalizePath($path);
    }

    public function save(File $path): void
    {
        if (!$this->isNormalized()) {
            $this->normalize($path);
        }

        $path->save();
    }

    /**
     * Get reports that can be fixed automatically.
     *
     * @return array<int,string>
     */
    public function getReports(): array
    {
        return $this->normalizationObserver->getReports();
    }

    /**
     * Get reports which cannot be fixed automatically.
     *
     * @return array<int,string>
     */
    public function getReportsWithManuals(): array
    {
        return $this->normalizationObserver->getReportsWithManuals();
    }

    public function isNormalized(): bool
    {
        return [] !== array_merge($this->getReports(), $this->getReportsWithManuals());
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

        // Trim redundant leading newlines.
        if (false !== $this->options['leading-eol']) {
            // Empty files depend on previous trimming of the final newlines.
            if (
                '' !== trim($path->getNewContent(), "\r\n")
                || ('' === trim($path->getNewContent(), "\r\n") && !$this->options['final-eol'])
            ) {
                $path = $this->normalizationFactory->make('leading-eol')->normalize($path);
            }
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
        if (!isset($this->options['path-name']) || false === $this->options['path-name']) {
            return $path;
        }

        $path = $this->normalizationFactory->make('extension')->normalize($path);

        return $this->normalizationFactory->make('path-name')->normalize($path);
    }

    private function normalizePermissions(File $path): File
    {
        if (!isset($this->options['permissions']) || false === $this->options['permissions']) {
            return $path;
        }

        return $this->normalizationFactory->make('permissions')->normalize($path);
    }
}
