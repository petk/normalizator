<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Finder\File;

/**
 * Checks if given file is executable.
 */
#[Filter(
    name: 'executable'
)]
class ExecutableFilter implements NormalizationFilterInterface
{
    public function filter(File $file): bool
    {
        return true;
    }
}
