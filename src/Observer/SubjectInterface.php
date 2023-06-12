<?php

declare(strict_types=1);

namespace Normalizator\Observer;

/**
 * This is the subject in the observer design pattern.
 *
 * The notify() method has string argument unlike in the \SplSubject interface.
 */
interface SubjectInterface
{
    public function attach(ObserverInterface $observer): void;

    public function detach(ObserverInterface $observer): void;

    public function notify(string $message, ?string $type = null): void;
}
