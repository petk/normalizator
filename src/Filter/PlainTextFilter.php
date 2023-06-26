<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Cache\Cache;
use Normalizator\Finder\File;

use function Normalizator\preg_match;

/**
 * Must be plain-text file.
 */
#[Filter(
    name: 'plain_text'
)]
class PlainTextFilter implements NormalizationFilterInterface
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
        $mimeType = $file->getMimeType();

        if ('inode/x-empty' === $mimeType) {
            return true;
        }

        return 1 === preg_match('/^text\//', $mimeType);
    }
}
