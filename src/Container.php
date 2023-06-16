<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Exception\ContainerEntryNotFoundException;
use Normalizator\Exception\ContainerException;
use Psr\Container\ContainerInterface;

/**
 * PSR-11 compatible dependency injection container.
 */
class Container implements ContainerInterface
{
    /**
     * All defined services and parameters.
     *
     * @var array<string,mixed>
     */
    public array $entries = [];

    /**
     * Already retrieved items are stored for faster retrievals in the same run.
     *
     * @var array<string,mixed>
     */
    private array $store = [];

    /**
     * Services already created to prevent circular references.
     *
     * @var array<string,bool>
     */
    private array $locks = [];

    /**
     * Class constructor.
     *
     * @param array<string,mixed> $configurations
     */
    public function __construct(array $configurations = [])
    {
        $this->entries = $configurations;
    }

    /**
     * Set service.
     */
    public function set(string $key, mixed $entry): void
    {
        $this->entries[$key] = $entry;
    }

    /**
     * Get entry.
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new ContainerEntryNotFoundException($id . ' entry not found.');
        }

        if (!isset($this->store[$id])) {
            $this->store[$id] = $this->createEntry($id);
        }

        return $this->store[$id];
    }

    /**
     * Check if entry is available in the container.
     */
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * Create new entry - service or configuration parameter.
     */
    private function createEntry(string $id): mixed
    {
        $entry = &$this->entries[$id];

        // Entry is a configuration parameter.
        if (!class_exists($id) && !is_callable($entry)) {
            return $entry;
        }

        // Invalid entry.
        if (class_exists($id) && !is_callable($entry)) {
            throw new ContainerException($id . ' entry must be callable.');
        }

        // Circular reference.
        if (class_exists($id) && isset($this->locks[$id])) {
            throw new ContainerException($id . ' entry contains a circular reference.');
        }

        $this->locks[$id] = true;

        /** @var callable $entry */
        return $entry($this);
    }
}
