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
     * @param array<string,mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        $this->configuration = array_merge($this->configuration, $configuration);
    }

    public function normalize(File $file): File
    {
        // Do normalization on the given file.

        return $file;
    }
}
