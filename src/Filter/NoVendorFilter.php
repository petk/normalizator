<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Cache\Cache;
use Normalizator\Finder\File;

use function file_exists;
use function is_dir;
use function Normalizator\preg_match;

/**
 * Filter which doesn't pass files within the vendor directory created by PHP
 * Composer.
 */
#[Filter(
    name: 'no_vendor',
)]
class NoVendorFilter implements NormalizationFilterInterface
{
    public function __construct(private Cache $cache) {}

    public function filter(File $file): bool
    {
        $key = static::class . ':' . $file->getPathname();

        if ($this->cache->has($key)) {
            return (bool) $this->cache->get($key);
        }

        $filter = $this->check($file);

        $this->cache->set($key, $filter);

        return $filter;
    }

    protected function check(File $file): bool
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
