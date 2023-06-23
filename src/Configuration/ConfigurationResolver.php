<?php

declare(strict_types=1);

namespace Normalizator\Configuration;

use Symfony\Component\Console\Exception\InvalidOptionException;

/**
 * Validates given options and returns resolved configuration values.
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

    /**
     * Resolve command line options into configuration values.
     *
     * @param array<string,null|array<mixed>|bool|float|int|string> $options
     *
     * @return array<string,null|array<mixed>|bool|float|int|string>
     */
    public function resolve(array $options): array
    {
        $configuration = [];

        // Set missing options.
        foreach ($this->options as $option) {
            if (!isset($options[$option])) {
                $options[$option] = null;
            }
        }

        // Check if no options are passed, which means to enable all.
        $all = true;
        foreach ($options as $key => $option) {
            if (in_array($key, $this->options, true) && false !== $option) {
                $all = false;
            }
        }

        if (true === $all) {
            $options['eol'] = 'lf';
            $options['final-eol'] = 1;
            $options['middle-eol'] = 1;

            foreach ($this->options as $key) {
                if (false === $options[$key]) {
                    $options[$key] = true;
                }
            }
        }

        $options['eol'] = $this->resolveEol($options['eol']);

        $configuration['encoding_callback'] = null;

        $options['final-eol'] = $this->resolveFinalEol($options['final-eol']);

        if (false !== $options['final-eol']) {
            $configuration['max_extra_final_eols'] = $options['final-eol'];
        }

        $options['middle-eol'] = $this->resolveMiddleEol($options['middle-eol']);

        if (false !== $options['middle-eol']) {
            $configuration['max_extra_middle_eols'] = $options['middle-eol'];
        }

        // Convert dashes to underscores for configuration parameters usage.
        foreach ($options as $key => $value) {
            if (in_array($key, $this->options, true)) {
                $configuration[str_replace('-', '_', $key)] = $value;
            }
        }

        return $configuration;
    }

    /**
     * Resolve the --eol option.
     *
     * @param null|array<mixed>|bool|float|int|string $eol
     *
     * @throws InvalidOptionException
     */
    private function resolveEol(null|array|bool|float|int|string $eol): bool|string
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

        return strtolower($eol);
    }

    /**
     * Resolve the --final-eol option.
     *
     * @param null|array<mixed>|bool|float|int|string $finalEol
     *
     * @throws InvalidOptionException
     */
    private function resolveFinalEol(null|array|bool|float|int|string $finalEol): bool|int
    {
        if (false === $finalEol) {
            return false;
        }

        if (null === $finalEol) {
            $finalEol = 1;
        }

        if (
            false === \filter_var($finalEol, \FILTER_VALIDATE_INT)
            || 0 > $finalEol
        ) {
            throw new InvalidOptionException('--final-eol can be either empty, 0, or positive integer.');
        }

        return (int) $finalEol;
    }

    /**
     * Resolve the --middle-eol option.
     *
     * @param null|array<mixed>|bool|float|int|string $middleEol
     *
     * @throws InvalidOptionException
     */
    private function resolveMiddleEol(null|array|bool|float|int|string $middleEol): bool|int
    {
        if (false === $middleEol) {
            return false;
        }

        if (null === $middleEol) {
            $middleEol = 1;
        }

        if (
            false === \filter_var($middleEol, \FILTER_VALIDATE_INT)
            || 0 > $middleEol
        ) {
            throw new InvalidOptionException('--middle-eol can be either empty, 0, or positive integer.');
        }

        return (int) $middleEol;
    }
}
