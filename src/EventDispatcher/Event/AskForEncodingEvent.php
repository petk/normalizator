<?php

declare(strict_types=1);

namespace Normalizator\EventDispatcher\Event;

use Normalizator\Finder\File;

/**
 * Generic debugging event for handling errors and debug messages.
 */
class AskForEncodingEvent
{
    private string $encoding;

    public function __construct(private File $file, private string $defaultEncoding = '')
    {
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getDefaultEncoding(): string
    {
        return $this->defaultEncoding ?? '';
    }

    public function setEncoding(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    public function getEncoding(): string
    {
        return strtolower($this->encoding ?? '');
    }
}
