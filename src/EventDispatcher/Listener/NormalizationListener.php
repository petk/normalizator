<?php

declare(strict_types=1);

namespace Normalizator\EventDispatcher\Listener;

use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\Util\Logger;

class NormalizationListener
{
    public function __construct(private Logger $logger)
    {
    }

    public function __invoke(object $event): void
    {
        if ($event instanceof NormalizationEvent) {
            $this->logger->add($event->getFile(), $event->getMessage(), $event->getType());
        }
    }
}
