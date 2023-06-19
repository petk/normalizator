<?php

declare(strict_types=1);

namespace Normalizator\Configuration;

/**
 * Configuration container for normalization options and configuration retrieved
 * from various resources.
 */
class Configuration
{
    /**
     * @var array<string,mixed>
     */
    private array $values;

    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    /**
     * @param array<string,mixed> $values
     */
    public function setMultiple(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @return array<string,mixed>
     */
    public function getAll(): array
    {
        return $this->values ?? [];
    }
}
