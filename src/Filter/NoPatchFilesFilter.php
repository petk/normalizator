<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Cache\Cache;
use Normalizator\Finder\File;

use function in_array;
use function strtolower;

/**
 * Filter which doesn't pass if the file is *.patch.
 */
#[Filter(
    name: 'no_patch_files',
)]
class NoPatchFilesFilter implements NormalizationFilterInterface
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
        return !in_array(strtolower($file->getExtension()), [
            'patch',
            'diff',
        ], true);
    }
}
