<?php

declare(strict_types=1);

namespace Normalizator\EventDispatcher\Listener;

use Normalizator\EventDispatcher\Event\DebugEvent;
use Normalizator\Util\Logger;

/**
 * Listener that listens for error and debugging messages events through the
 * application.
 */
class DebugListener
{
    public function __construct(private Logger $logger)
    {
    }

    public function __invoke(object $event): void
    {
        if ($event instanceof DebugEvent) {
            $this->logger->addDebugMessage($event->getMessage());
        }
    }
}
