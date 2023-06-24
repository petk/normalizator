<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Attribute\Normalization;
use Normalizator\Exception\ContainerEntryNotFoundException;
use Normalizator\Finder\Finder;
use Normalizator\Normalization\ConfigurableNormalizationInterface;
use Normalizator\Normalization\NormalizationInterface;

/**
 * A simple factory that registers and creates all normalizations.
 */
class NormalizationFactory
{
    /**
     * Array of registered normalization classes.
     *
     * @var array<string,class-string<NormalizationInterface>>
     */
    private array $normalizationsRegister = [];

    /**
     * Array of initialized normalization objects.
     *
     * @var array<string,NormalizationInterface>
     */
    private array $normalizations = [];

    public function __construct(
        private readonly Finder $finder,
        private readonly Container $container,
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
                $normalization->configure(...$configuration);
            }

            return $normalization;
        }

        $class = $this->normalizationsRegister[$name];

        $dependencies = $this->getDependencies($class);

        $normalization = new $class(...$dependencies);

        if ($normalization instanceof ConfigurableNormalizationInterface) {
            $normalization->configure(...$configuration);
        }

        return $this->normalizations[$name] = $normalization;
    }

    private function registerNormalizations(): void
    {
        foreach ($this->finder->getTree(__DIR__ . '/Normalization') as $normalization) {
            if (
                'php' !== $normalization->getExtension()
                || 'Normalization.php' !== substr($normalization->getFilename(), -17)
            ) {
                continue;
            }

            $class = substr($normalization->getSubPathname(), 0, -4);
            $class = str_replace('/', '\\', $class);

            /** @var class-string<NormalizationInterface> */
            $class = 'Normalizator\\Normalization\\' . $class;

            // Resolve normalization attributes.
            $reflection = new \ReflectionClass($class);
            foreach ($reflection->getAttributes() as $attribute) {
                if (Normalization::class === $attribute->getName()) {
                    $arguments = $attribute->getArguments();
                    if (!isset($arguments['name'])) {
                        throw new \Exception('The name attribute is required for Normalization ' . $class);
                    }

                    $name = (string) $arguments['name'];

                    $this->normalizationsRegister[$name] = $class;

                    break;
                }
            }
        }
    }

    /**
     * Resolve normalization dependencies.
     *
     * @param class-string $class
     *
     * @return array<int,mixed>
     */
    private function getDependencies(string $class): array
    {
        $dependencies = [];

        $ref = new \ReflectionClass($class);
        $constructor = $ref->getConstructor();

        if (null === $constructor) {
            return $dependencies;
        }

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (null !== $type && $type instanceof \ReflectionNamedType) {
                try {
                    $dependencies[] = $this->container->get($type->getName());
                } catch (ContainerEntryNotFoundException $e) {
                    continue;
                }
            }
        }

        return $dependencies;
    }
}
