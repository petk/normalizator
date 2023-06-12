<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Finder\File;

/**
 * Must be file.
 */
#[Filter(
    name: 'file'
)]
class FileFilter implements NormalizationFilterInterface
{
    public function filter(File $file): bool
    {
        return $file->isFile();
    }
}
