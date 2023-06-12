<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Finder\File;

use function Normalizator\preg_match;

/**
 * Filter which doesn't pass the SVN directory.
 */
#[Filter(
    name: 'no-svn'
)]
class NoSvnFilter implements NormalizationFilterInterface
{
    public function filter(File $file): bool
    {
        return 1 !== preg_match('/(^|\/)\.svn(|\/.+)$/', $file->getPathname());
    }
}
