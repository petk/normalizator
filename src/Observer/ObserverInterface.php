<?php

declare(strict_types=1);

namespace Normalizator\Observer;

use Normalizator\Finder\File;

/**
 * Observer in the observer design pattern.
 *
 * Instead of the PHP SPL \SplObserver this one uses custom update method with
 * string passed to the update.
 */
interface ObserverInterface
{
    public function update(File $file, string $message, ?string $type = null): void;
}
