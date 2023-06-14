<?php

declare(strict_types=1);

namespace Normalizator\Observer;

use Normalizator\Finder\File;

class NormalizationObserver implements ObserverInterface
{
    /**
     * Array with reports that can be normalized.
     *
     * @var array<string,array<int,string>>
     */
    private array $reports = [];

    /**
     * Array with reports for things that cannot be normalized.
     *
     * @var array<string,array<int,string>>
     */
    private array $errors = [];

    /**
     * It is called by the Subject, usually by SplSubject::notify().
     */
    public function update(File $file, string $message, ?string $type = null): void
    {
        if ('error' === $type) {
            $this->errors[$file->getPathname()] ??= [];
            $this->errors[$file->getPathname()][] = $message;

            return;
        }

        $this->reports[$file->getPathname()] ??= [];
        $this->reports[$file->getPathname()][] = $message;
    }

    /**
     * Get reports for given file that can be normalized.
     *
     * @return array<int,string>
     */
    public function getReports(File $file): array
    {
        return array_unique($this->reports[$file->getPathname()] ?? []);
    }

    /**
     * Get all reports that can be normalized.
     *
     * @return array<string,array<int,string>>
     */
    public function getAllReports(): array
    {
        return $this->reports;
    }

    /**
     * Get reports of issues for given file that cannot be normalized.
     *
     * @return array<int,string>
     */
    public function getErrors(File $file): array
    {
        return array_unique($this->errors[$file->getPathname()] ?? []);
    }

    /**
     * Get all reports of issues that cannot be normalized.
     *
     * @return array<string,array<int,string>>
     */
    public function getAllErrors(): array
    {
        return $this->errors;
    }

    /**
     * Clean all reports.
     */
    public function clean(): void
    {
        $this->reports = [];
        $this->errors = [];
    }
}
