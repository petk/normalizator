<?php

declare(strict_types=1);

namespace Normalizator\Configuration;

/**
 * Configures application.
 */
class Configurator
{
    public function __construct(
        private Configuration $configuration,
        private ConfigurationResolver $configurationResolver,
    ) {
    }

    /**
     * Set all configuration from given resources.
     *
     * @param array<string,null|array<mixed>|bool|float|int|string> $options
     */
    public function set(array $options): void
    {
        // Start a new configuration set.
        $this->configuration->clear();

        // Resolve configuration values.
        $configurations = $this->configurationResolver->resolve($options);

        // Set configuration values.
        $this->configuration->setMultiple($configurations);
    }
}
