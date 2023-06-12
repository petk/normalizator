<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Finder\File;

/**
 * Must not be link.
 *
 * Links are special. They can set permissions on the target which we shouldn't
 * do that here. So we just filter links out on certain normalizations.
 */
#[Filter(
    name: 'no-links'
)]
class NoLinksFilter implements NormalizationFilterInterface
{
    public function filter(File $file): bool
    {
        return !$file->isLink();
    }
}
