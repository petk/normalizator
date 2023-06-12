<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Finder\File;

/**
 * Interface for normalizations.
 */
interface NormalizationInterface
{
    /**
     * Check if given file should be normalized or not.
     */
    public function filter(File $file): bool;

    /**
     * Normalizes given file.
     */
    public function normalize(File $file): File;
}
