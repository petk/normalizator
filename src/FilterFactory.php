<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Attribute\Filter;
use Normalizator\Cache\Cache;
use Normalizator\Filter\NormalizationFilterInterface;
use Normalizator\Finder\Finder;
use Normalizator\Util\GitDiscovery;
use ReflectionClass;

use function array_push;
use function is_string;
use function substr;

/**
 * Factory for creating normalization filters.
 */
class FilterFactory
{
    /**
     * Array of registered filter classes.
     *
     * @var array<string,class-string<NormalizationFilterInterface>>
     */
    private array $filterRegistry = [];

    /**
     * Array of initialized filter objects.
     *
     * @var array<string,NormalizationFilterInterface>
     */
    private array $filters = [];

    public function __construct(
        private Finder $finder,
        private Cache $cache,
        private GitDiscovery $gitDiscovery,
    ) {
        $this->registerFilters();
    }

    /**
     * Normalization filter maker.
     */
    public function make(string $name): NormalizationFilterInterface
    {
        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        }

        $class = $this->filterRegistry[$name];
        $dependencies = [$this->cache];

        switch ($name) {
            case 'no_git':
                array_push(
                    $dependencies,
                    $this->gitDiscovery,
                );

                break;
        }

        return $this->filters[$name] = new $class(...$dependencies);
    }

    private function registerFilters(): void
    {
        foreach ($this->finder->getTree(__DIR__ . '/Filter') as $filter) {
            if ('php' === $filter->getExtension()
                && 'Filter.php' === substr($filter->getFilename(), -10)
            ) {
                /** @var class-string<NormalizationFilterInterface> */
                $class = 'Normalizator\\Filter\\' . substr($filter->getFilename(), 0, -4);

                $reflection = new ReflectionClass($class);
                foreach ($reflection->getAttributes() as $attribute) {
                    if (Filter::class === $attribute->getName()) {
                        $arguments = $attribute->getArguments();
                        if (isset($arguments['name']) && is_string($arguments['name'])) {
                            $this->filterRegistry[$arguments['name']] = $class;
                        }

                        break;
                    }
                }
            }
        }
    }
}
