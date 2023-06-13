<?php

declare(strict_types=1);

namespace Normalizator\Util;

/**
 * Utility that tracks script execution time.
 */
class Timer
{
    private float $time;

    public function __construct()
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
