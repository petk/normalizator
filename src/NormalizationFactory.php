<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Attribute\Normalization;
use Normalizator\Finder\Finder;
use Normalizator\Normalization\NormalizationInterface;
use Normalizator\Observer\NormalizationObserver;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Slugify;

/**
 * A simple factory that registers and creates all normalizations.
 */
class NormalizationFactory
{
    /**
     * Array of registered normalization classes.
     *
     * @var array<string,array<int,array<int,string>|string>>
     */
    private array $normalizationsRegister = [];

    /**
     * Array of initialized normalization objects.
     *
     * @var array<string,object>
     */
    private array $normalizations = [];

    public function __construct(
        private Finder $finder,
        private Slugify $slugify,
        private EolDiscovery $eolDiscovery,
        private GitDiscovery $gitDiscovery,
        private FilenameResolver $filenameResolver,
        private FilterFactory $filterFactory,
        private NormalizationObserver $normalizationObserver,
    ) {
        $this->registerNormalizations();
    }

    /**
     * @param array<string,mixed> $configuration
     */
    public function make(string $name, array $configuration = []): NormalizationInterface
    {
        if (isset($this->normalizations[$name])) {
            $normalization = $this->normalizations[$name];
            $normalization->configure($configuration);

            return $normalization;
        }

        $class = $this->normalizationsRegister[$name][0];

        $dependencies = [];

        // Normalizations with dependencies.
        switch ($name) {
            case 'eol':
                array_push(
                    $dependencies,
                    $this->eolDiscovery,
                );

                break;

            case 'extension':
                array_push(
                    $dependencies,
                    $this->filenameResolver,
                );

                break;

            case 'final-eol':
                array_push(
                    $dependencies,
                    $this->eolDiscovery,
                );

                break;

            case 'path-name':
                array_push(
                    $dependencies,
                    $this->slugify,
                    $this->filenameResolver,
                );

                break;

            case 'permissions':
                array_push(
                    $dependencies,
                    $this->gitDiscovery,
                );

                break;
        }

        $normalization = new $class(...$dependencies);
        $normalization->configure($configuration);

        // Add filters.
        $filters = [];
        foreach ($this->normalizationsRegister[$name][1] as $filter) {
            $filters[] = $this->filterFactory->make($filter);
        }
        $normalization->addFilters($filters);

        $normalization->attach($this->normalizationObserver);

        return $this->normalizations[$name] = $normalization;
    }

    private function registerNormalizations(): void
    {
        foreach ($this->finder->getTree(__DIR__ . '/Normalization') as $normalization) {
            if (
                'php' !== $normalization->getExtension()
                || 'Normalization.php' !== substr($normalization->getFilename(), -17)
                && 'AbstractNormalization.php' === $normalization->getFilename()
            ) {
                continue;
            }

            $class = substr($normalization->getSubPathname(), 0, -4);
            $class = str_replace('/', '\\', $class);
            $class = 'Normalizator\\Normalization\\' . $class;

            $reflection = new \ReflectionClass($class);
            foreach ($reflection->getAttributes() as $attribute) {
                if (Normalization::class === $attribute->getName()) {
                    $arguments = $attribute->getArguments();
                    if (!isset($arguments['name'])) {
                        throw new \Exception('The name attribute is required for Normalization ' . $class);
                    }

                    $name = $arguments['name'];
                    $filters = $arguments['filters'] ?? [];

                    $this->normalizationsRegister[$name] = [$class, $filters];

                    break;
                }
            }
        }
    }
}
