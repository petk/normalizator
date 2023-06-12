<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Finder\File;

use function Normalizator\preg_match;

/**
 * Filter which doesn't pass files within the vendor directory created by PHP
 * Composer.
 */
#[Filter(
    name: 'no-vendor'
)]
class NoVendorFilter implements NormalizationFilterInterface
{
    public function filter(File $file): bool
    {
        $result = preg_match('/(^|.*?\/)vendor($|\/.*)/', $file->getSubPathname(), $matches);

        if (
            1 === $result
            && isset($matches[1])
            && file_exists($file->getRootPath() . '/' . $matches[1] . 'vendor/autoload.php')
            && is_dir($file->getRootPath() . '/' . $matches[1] . 'vendor/composer')
        ) {
            return false;
        }

        return true;
    }
}
