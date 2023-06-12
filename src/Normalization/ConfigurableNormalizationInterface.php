<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

/**
 * Interface for normalizations where.
 */
interface ConfigurableNormalizationInterface extends NormalizationInterface
{
    /**
     * Set configuration for the normalization.
     *
     * @param array<string,null|array<mixed>|bool|float|int|string> $configuration
     */
    public function configure(array $configuration): void;
}
