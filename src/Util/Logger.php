<?php

declare(strict_types=1);

namespace Normalizator\Util;

use Normalizator\Finder\File;

/**
 * Logger utility that holds messages from normalizations and file changes.
 */
class Logger
{
    /**
     * Array with log messages.
     *
     * @var array<string,array<int,string>>
     */
    private array $logs = [];

    /**
     * Array with error messages where files cannot be normalized.
     *
     * @var array<string,array<int,string>>
     */
    private array $errors = [];

    /**
     * Miscellaneous debug and error messages throughout the application.
     *
     * @var array<int,string>
     */
    private array $debug = [];

    /**
     * Add new message in the logs array for given file.
     */
    public function add(File $file, string $message, string $type = 'log'): void
    {
        if ('error' === $type) {
            $this->errors[$file->getPathname()] ??= [];
            $this->errors[$file->getPathname()][] = $message;

            return;
        }

        $this->logs[$file->getPathname()] ??= [];
        $this->logs[$file->getPathname()][] = $message;
    }

    /**
     * Add debug message.
     */
    public function addDebugMessage(string $message): void
    {
        $this->debug[] = $message;
    }

    /**
     * Get logs for given file that can be normalized.
     *
     * @return array<int,string>
     */
    public function getLogs(File $file): array
    {
        return array_unique($this->logs[$file->getPathname()] ?? []);
    }

    /**
     * Get all logs that can be normalized.
     *
     * @return array<string,array<int,string>>
     */
    public function getAllLogs(): array
    {
        return $this->logs;
    }

    /**
     * Get logs of issues for given file that cannot be normalized.
     *
     * @return array<int,string>
     */
    public function getErrors(File $file): array
    {
        return array_unique($this->errors[$file->getPathname()] ?? []);
    }

    /**
     * Get all issues that cannot be normalized.
     *
     * @return array<string,array<int,string>>
     */
    public function getAllErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get all debug messages.
     *
     * @return array<int,string>
     */
    public function getDebugMessages(): array
    {
        return $this->debug;
    }

    /**
     * Clear all logs.
     */
    public function clear(): void
    {
        $this->logs = [];
        $this->errors = [];
        $this->debug = [];
    }
}
