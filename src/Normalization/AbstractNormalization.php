<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Finder\File;

/**
 * Abstract normalization.
 */
abstract class AbstractNormalization implements NormalizationInterface
{
    /**
     * @var array<string,mixed>
     */
    protected array $configuration = [];

    /**
     * @var array<int,\Normalizator\Filter\NormalizationFilterInterface>
     */
    protected array $filters = [];

    /**
     * @param array<string,mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        $this->configuration = array_merge($this->configuration, $configuration);
    }

    /**
     * @param array<int,\Normalizator\Filter\NormalizationFilterInterface> $filters
     */
    public function addFilters(array $filters): void
    {
        $this->filters = array_merge($this->filters, $filters);
    }

    public function filter(File $file): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter->filter($file)) {
                return false;
            }
        }

        return true;
    }

    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        // Do normalization on the given file.

        return $file;
    }
}
