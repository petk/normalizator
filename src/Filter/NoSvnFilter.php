<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Cache\Cache;
use Normalizator\Finder\File;

use function Normalizator\preg_match;

/**
 * Filter which doesn't pass the SVN directory.
 */
#[Filter(
    name: 'no_svn'
)]
class NoSvnFilter implements NormalizationFilterInterface
{
    public function __construct(private Cache $cache)
    {
    }

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
        return 1 !== preg_match('/(^|\/)\.svn(|\/.+)$/', $file->getPathname());
    }
}
