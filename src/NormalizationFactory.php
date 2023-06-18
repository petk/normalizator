<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\Finder;
use Normalizator\Normalization\ConfigurableNormalizationInterface;
use Normalizator\Normalization\NormalizationInterface;
use Normalizator\Util\EolDiscovery;
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
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher,
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

            if ($normalization instanceof ConfigurableNormalizationInterface) {
                $normalization->configure($configuration);
            }

            return $normalization;
        }

        $class = $this->normalizationsRegister[$name];

        $dependencies = [$this->filterManager, $this->eventDispatcher];

        // Normalizations with dependencies.
        switch ($name) {
            case 'eol':
                array_push(
                    $dependencies,
                    $this->eolDiscovery,
                );

                break;

            case 'final-eol':
                array_push(
                    $dependencies,
                    $this->eolDiscovery,
                );

                break;

            case 'name':
                array_push(
                    $dependencies,
                    $this->slugify
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

        if ($normalization instanceof ConfigurableNormalizationInterface) {
            $normalization->configure($configuration);
        }

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

                    $this->normalizationsRegister[$name] = $class;

                    break;
                }
            }
        }
    }
}
