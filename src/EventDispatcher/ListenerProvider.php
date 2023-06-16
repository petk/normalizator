<?php

declare(strict_types=1);

namespace Normalizator\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Listener provider.
 */
class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array<string,array<int,callable>>
     */
    private array $listeners = [];

    /**
     * @return array<int,callable>>
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventType = $event::class;

        if (array_key_exists($eventType, $this->listeners)) {
            return $this->listeners[$eventType];
        }

        return [];
    }

    public function addListener(string $eventType, callable $callable): self
    {
        $this->listeners[$eventType][] = $callable;

        return $this;
    }

    public function clearListeners(string $eventType): void
    {
        if (array_key_exists($eventType, $this->listeners)) {
            unset($this->listeners[$eventType]);
        }
    }
}
