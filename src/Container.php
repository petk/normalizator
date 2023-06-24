<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Exception\ContainerCircularDependencyException;
use Normalizator\Exception\ContainerEntryNotFoundException;
use Normalizator\Exception\ContainerInvalidEntryException;
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
    private array $entries = [];

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
     * @param array<string,mixed> $entries A list of initial parameters.
     */
    public function __construct(array $entries = [])
    {
        foreach ($entries as $id => $entry) {
            $this->set($id, $entry);
        }
    }

    /**
     * Set entry to container.
     *
     * @throws ContainerInvalidEntryException
     */
    public function set(string $id, mixed $entry): void
    {
        // Invalid entry.
        if (class_exists($id) && !is_callable($entry)) {
            throw new ContainerInvalidEntryException(
                sprintf('Entry %s must be callable.', $id)
            );
        }

        $this->entries[$id] = $entry;
    }

    /**
     * @throws ContainerCircularDependencyException
     * @throws ContainerEntryNotFoundException
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
     *
     * @throws ContainerCircularDependencyException
     */
    private function createEntry(string $id): mixed
    {
        $entry = &$this->entries[$id];

        // Entry is a configuration parameter.
        if (!class_exists($id) && !is_callable($entry)) {
            return $entry;
        }

        // Circular reference.
        if (class_exists($id) && isset($this->locks[$id])) {
            throw new ContainerCircularDependencyException($id . ' entry contains a circular reference.');
        }

        $this->locks[$id] = true;

        /** @var callable $entry */
        return $entry($this);
    }
}
