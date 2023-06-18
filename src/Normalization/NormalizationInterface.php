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
     * Normalizes given file.
     */
    public function normalize(File $file): File;
}
