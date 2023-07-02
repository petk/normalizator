<?php

declare(strict_types=1);

namespace Normalizator\Configuration;

use Normalizator\Normalization\IndentationNormalization;
use Symfony\Component\Console\Exception\InvalidOptionException;

use function filter_var;
use function in_array;
use function is_string;
use function str_replace;
use function strtolower;

use const FILTER_VALIDATE_INT;

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
        'indentation',
        'indentation-size',
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
            $options['indentation'] = null;
            $options['indentation-size'] = IndentationNormalization::INDENTATION_SIZE;
            $options['middle-eol'] = 1;

            foreach ($this->options as $key) {
                if (false === $options[$key]) {
                    $options[$key] = true;
                }
            }
        }

        $options['eol'] = $this->resolveEol($options['eol']);
        $options['indentation'] = $this->resolveIndentation($options['indentation']);
        $options['indentation-size'] = $this->resolveIndentationSize($options['indentation-size']);

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
            false === filter_var($finalEol, FILTER_VALIDATE_INT)
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
            false === filter_var($middleEol, FILTER_VALIDATE_INT)
            || 0 > $middleEol
        ) {
            throw new InvalidOptionException('--middle-eol can be either empty, 0, or positive integer.');
        }

        return (int) $middleEol;
    }

    /**
     * Resolve the --indentation option.
     *
     * @param null|array<mixed>|bool|float|int|string $indentation
     *
     * @throws InvalidOptionException
     */
    private function resolveIndentation(null|array|bool|float|int|string $indentation): bool|string
    {
        if (false === $indentation) {
            return false;
        }

        if (null === $indentation) {
            $indentation = 'space';
        }

        if (
            !is_string($indentation)
            || !in_array(strtolower($indentation), ['space', 'tab'], true)
        ) {
            throw new InvalidOptionException('--indentation can be either "space" or "tab".');
        }

        $indentation = strtolower($indentation);

        $map = ['space' => ' ', 'tab' => "\t"];

        return $map[$indentation];
    }

    /**
     * Resolve the --indentation-size option.
     *
     * @param null|array<mixed>|bool|float|int|string $size
     *
     * @throws InvalidOptionException
     */
    private function resolveIndentationSize(null|array|bool|float|int|string $size): int
    {
        if (false === $size) {
            return IndentationNormalization::INDENTATION_SIZE;
        }

        if (null === $size) {
            $size = IndentationNormalization::INDENTATION_SIZE;
        }

        if (
            false === filter_var($size, FILTER_VALIDATE_INT)
            || 1 > $size
        ) {
            throw new InvalidOptionException('--indentation-size must be integer greater than 0.');
        }

        return (int) $size;
    }
}
