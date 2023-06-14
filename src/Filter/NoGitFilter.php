<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Cache\Cache;
use Normalizator\Finder\File;
use Normalizator\Util\GitDiscovery;

/**
 * Filter which doesn't pass Git directory or bare Git directory.
 */
#[Filter(
    name: 'no-git'
)]
class NoGitFilter implements NormalizationFilterInterface
{
    public function __construct(
        private Cache $cache,
        private GitDiscovery $gitDiscovery
    ) {
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
        return !$this->gitDiscovery->isInGit($file);
    }
}
