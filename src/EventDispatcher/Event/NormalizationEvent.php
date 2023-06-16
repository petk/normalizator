<?php

declare(strict_types=1);

namespace Normalizator\EventDispatcher\Event;

use Normalizator\Finder\File;

/**
 * Event for normalizations.
 */
class NormalizationEvent
{
    public function __construct(private File $file, private string $message, private string $type = 'log')
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
