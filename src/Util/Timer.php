<?php

declare(strict_types=1);

namespace Normalizator\Util;

use function microtime;
use function round;

/**
 * Utility that tracks script execution time.
 *
 * Timer is automatically started when initializing the Timer object. Otherwise
 * it can be started manually with the Timer::start() method for cases where
 * you need to measure script execution before initializing the timer as
 * injected dependency in the container.
 */
class Timer
{
    private float $time;

    public function __construct()
    {
        $this->time = microtime(true);
    }

    public function start(): void
    {
        $this->time = microtime(true);
    }

    /**
     * Get current execution time in seconds.
     */
    public function stop(): float
    {
        return round(microtime(true) - $this->time, 3);
    }
}
