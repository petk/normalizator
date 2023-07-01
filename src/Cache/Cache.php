<?php

declare(strict_types=1);

namespace Normalizator\Cache;

use Normalizator\Exception\CacheInvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

/**
 * PSR-16 compatible simple cache utility.
 */
class Cache implements CacheInterface
{
    /**
     * @var array<string,mixed>
     */
    private array $values = [];

    /**
     * @var array<string,null|\DateInterval|int>
     */
    private array $ttl = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if ('' === $key) {
            throw new CacheInvalidArgumentException('Key must not be empty string');
        }

        return $this->values[$key] ?? $default;
    }

    public function set(string $key, mixed $value, null|\DateInterval|int $ttl = null): bool
    {
        if ('' === $key) {
            throw new CacheInvalidArgumentException('Key must not be empty string');
        }

        $this->values[$key] = $value;
        $this->ttl[$key] = $ttl;

        return true;
    }

    public function has(string $key): bool
    {
        if ('' === $key) {
            throw new CacheInvalidArgumentException('Key must not be empty string');
        }

        return isset($this->values[$key]);
    }

    public function delete(string $key): bool
    {
        if ('' === $key) {
            throw new CacheInvalidArgumentException('Key must not be empty string');
        }

        unset($this->values[$key], $this->ttl[$key]);

        return true;
    }

    public function clear(): bool
    {
        $this->values = [];
        $this->ttl = [];

        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $return = [];

        foreach ($keys as $key) {
            if ('' === $key) {
                throw new CacheInvalidArgumentException('Key must not be empty string');
            }

            $return[$key] = $this->values[$key] ?? $default;
        }

        return $return;
    }

    /**
     * @param \Traversable<string,mixed> $values
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            if ('' === $key) {
                throw new CacheInvalidArgumentException('Key must not be empty string');
            }

            $this->values[$key] = $value;
            $this->ttl[$key] = $ttl;
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            if ('' === $key) {
                throw new CacheInvalidArgumentException('Key must not be empty string');
            }

            unset($this->values[$key], $this->ttl[$key]);
        }

        return true;
    }
}
