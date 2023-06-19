<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

/**
 * Interface for normalizations where additional configuration is supported.
 */
interface ConfigurableNormalizationInterface extends NormalizationInterface
{
    /**
     * Set configuration for the normalization.
     *
     * @param array<string,mixed> $configuration
     */
    public function configure(array $configuration): void;
}
