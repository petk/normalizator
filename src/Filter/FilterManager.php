<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Normalization;
use Normalizator\FilterFactory;
use Normalizator\Finder\File;
use Normalizator\Normalization\NormalizationInterface;
use ReflectionClass;

/**
 * Checks if given normalization should be done on given file.
 */
class FilterManager
{
    /**
     * @var array<string,array<int,NormalizationFilterInterface>>
     */
    protected array $filters = [];

    public function __construct(
        private FilterFactory $filterFactory,
    ) {}

    public function filter(NormalizationInterface $normalization, File $file): bool
    {
        $filters = $this->getFilters($normalization);

        foreach ($filters as $filter) {
            if (!$filter->filter($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get filters for given normalization.
     *
     * @return array<int,NormalizationFilterInterface>
     */
    private function getFilters(NormalizationInterface $normalization): array
    {
        if (isset($this->filters[$normalization::class])) {
            return $this->filters[$normalization::class];
        }

        $filters = [];
        $reflection = new ReflectionClass($normalization::class);
        foreach ($reflection->getAttributes() as $attribute) {
            if (Normalization::class === $attribute->getName()) {
                $arguments = $attribute->getArguments();
                $filters = $arguments['filters'] ?? [];

                break;
            }
        }

        $this->filters[$normalization::class] = [];
        foreach ($filters as $filter) {
            $this->filters[$normalization::class][] = $this->filterFactory->make($filter);
        }

        return $this->filters[$normalization::class];
    }
}
