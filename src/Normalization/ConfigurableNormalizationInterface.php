<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

/**
 * Interface for normalizations where additional configuration is supported.
 */
interface ConfigurableNormalizationInterface extends NormalizationInterface
{
    /**
     * Set configuration options for the normalization.
     */
    public function configure(mixed ...$options): void;
}
