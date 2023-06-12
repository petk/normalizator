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
    name: 'no-node-modules'
)]
class NoNodeModulesFilter implements NormalizationFilterInterface
{
    public function filter(File $file): bool
    {
        $result = preg_match('/(^|.*?\/)node_modules($|\/.*)/', $file->getSubPathname(), $matches);

        if (
            1 === $result
            && isset($matches[1])
            && file_exists($file->getRootPath() . '/' . $matches[1] . 'node_modules/.package-lock.json')
        ) {
            return false;
        }

        return true;
    }
}
