<?php

declare(strict_types=1);

namespace Normalizator\Observer;

class NormalizationObserver implements ObserverInterface
{
    /**
     * Array with reports that can be normalized.
     *
     * @var array<int,string>
     */
    private array $reports = [];

    /**
     * Array with reports for things that cannot be normalized.
     *
     * @var array<int,string>
     */
    private array $reportsWithManuals = [];

    /**
     * It is called by the Subject, usually by SplSubject::notify().
     */
    public function update(string $message, ?string $type = null): void
    {
        if ('manual' === $type) {
            $this->reportsWithManuals[] = $message;

            return;
        }

        $this->reports[] = $message;
    }

    /**
     * Get all reports that can be normalized.
     *
     * @return array<int,string>
     */
    public function getReports(): array
    {
        return array_unique($this->reports);
    }

    /**
     * Get reports of issues that cannot be normalized.
     *
     * @return array<int,string>
     */
    public function getReportsWithManuals(): array
    {
        return array_unique($this->reportsWithManuals);
    }

    /**
     * Clean all reports.
     */
    public function clean(): void
    {
        $this->reports = [];
        $this->reportsWithManuals = [];
    }
}
