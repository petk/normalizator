<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Finder\File;

/**
 * Interface for normalizators.
 */
interface NormalizatorInterface
{
    /**
     * Set options for the normalizator.
     *
     * @param array<string,mixed> $options
     */
    public function setOptions(array $options): void;

    /**
     * Normalize given path.
     */
    public function normalize(File $path): void;

    /**
     * Normalize and save path.
     */
    public function save(File $path): void;

    /**
     * Check if normalizator has done some normalizations.
     */
    public function isNormalized(File $file): bool;
}
