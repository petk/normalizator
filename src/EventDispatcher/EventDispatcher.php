<?php

declare(strict_types=1);

namespace Normalizator\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * PSR-14 compatible event dispatcher.
 */
class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(private ListenerProviderInterface $provider) {}

    public function dispatch(object $event): object
    {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return $event;
        }

        foreach ($this->provider->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        return $event;
    }
}
