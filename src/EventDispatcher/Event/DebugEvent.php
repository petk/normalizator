<?php

declare(strict_types=1);

namespace Normalizator\EventDispatcher\Event;

/**
 * Generic debugging event for handling errors and debug messages.
 */
class DebugEvent
{
    public function __construct(private string $message) {}

    public function getMessage(): string
    {
        return $this->message;
    }
}
