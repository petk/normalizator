<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Finder\File;

/**
 * Interface for implementing normalization filters.
 */
interface NormalizationFilterInterface
{
    /**
     * Checks if given file should be normalized or not.
     *
     * Returns true if normalization should be done on the given file or false
     * if normalization shouldn't be done.
     */
    public function filter(File $file): bool;
}
