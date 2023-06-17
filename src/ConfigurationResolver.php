<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Util\EolDiscovery;
use Symfony\Component\Console\Exception\InvalidOptionException;

/**
 * Validates given configuration options and returns resolved ones.
 */
class ConfigurationResolver
{
    /**
     * These are entire set of normalizator options for check and fix commands.
     *
     * @var array<int,string>
     */
    private array $options = [
        'encoding',
        'eol',
        'extension',
        'final-eol',
        'leading-eol',
        'middle-eol',
        'name',
        'permissions',
        'space-before-tab',
        'trailing-whitespace',
    ];

    public function __construct(private EolDiscovery $eolDiscovery)
    {
    }

    /**
     * Resolve given configuration.
     *
     * @param array<string,null|array<mixed>|bool|float|int|string> $configuration
     *
     * @return array<string,null|array<mixed>|bool|float|int|string>
     */
    public function resolve(array $configuration): array
    {
        // Set missing options.
        foreach ($this->options as $option) {
            if (!isset($configuration[$option])) {
                $configuration[$option] = null;
            }
        }

        // Check if no options are passed, which means to enable all.
        $all = true;
        foreach ($configuration as $key => $option) {
            if (in_array($key, $this->options, true) && false !== $option) {
                $all = false;
            }
        }

        if (true === $all) {
            $configuration['eol'] = 'lf';
            $configuration['final-eol'] = 1;
            $configuration['middle-eol'] = 1;

            foreach ($this->options as $key) {
                if (false === $configuration[$key]) {
                    $configuration[$key] = true;
                }
            }
        }

        $configuration['eol'] = $this->resolveEol($configuration['eol']);

        // Set the --middle-eol value
        if (null === $configuration['middle-eol']) {
            $configuration['middle-eol'] = 1;
        }

        return $configuration;
    }

    /**
     * Resolve the --eol option.
     *
     * @throws InvalidOptionException
     */
    protected function resolveEol(mixed $eol): bool|string
    {
        // Option has not been set.
        if (false === $eol) {
            return $eol;
        }

        // The --eol option has been set without value. Set it to default.
        if (null === $eol) {
            return 'lf';
        }

        if (!is_string($eol) || !in_array(strtolower($eol), ['lf', 'crlf'], true)) {
            throw new InvalidOptionException('--eol can be either lf or crlf.');
        }

        $eol = strtolower($eol);

        // Set also EOL discovery utility.
        $map = ['lf' => "\n", 'crlf' => "\r\n"];
        $this->eolDiscovery->setEol($map[$eol]);

        return $eol;
    }
}
