<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Finder\File;
use Normalizator\Observer\ObserverInterface;
use Normalizator\Observer\SubjectInterface;

/**
 * Abstract normalization.
 */
abstract class AbstractNormalization implements NormalizationInterface, SubjectInterface
{
    /**
     * @var array<string,mixed>
     */
    protected array $configuration = [];

    /**
     * @var array<int,\Normalizator\Filter\NormalizationFilterInterface>
     */
    protected array $filters = [];
    protected \SplObjectStorage $observers;

    /**
     * @param array<string,mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        $this->configuration = array_merge($this->configuration, $configuration);
    }

    /**
     * @param array<int,\Normalizator\Filter\NormalizationFilterInterface> $filters
     */
    public function addFilters(array $filters): void
    {
        $this->filters = array_merge($this->filters, $filters);
    }

    public function filter(File $file): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter->filter($file)) {
                return false;
            }
        }

        return true;
    }

    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        // Do normalization on the given file.

        return $file;
    }

    public function attach(ObserverInterface $observer): void
    {
        if (!isset($this->observers)) {
            $this->observers = new \SplObjectStorage();
        }

        $this->observers->attach($observer);
    }

    public function detach(ObserverInterface $observer): void
    {
        if (!isset($this->observers)) {
            $this->observers = new \SplObjectStorage();
        }

        $this->observers->detach($observer);
    }

    public function notify(File $file, string $message, ?string $type = null): void
    {
        /** @var ObserverInterface $observer */
        foreach ($this->observers as $observer) {
            $observer->update($file, $message, $type);
        }
    }
}
