<?php

declare(strict_types=1);

namespace Normalizator\Tests;

use Normalizator\Container;
use Normalizator\Filter\NormalizationFilterInterface;
use Normalizator\FilterFactory;
use Normalizator\Normalization\NormalizationInterface;
use Normalizator\NormalizationFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

use function array_merge_recursive;

abstract class NormalizatorTestCase extends TestCase
{
    protected Container $container;
    protected string $fixturesRoot;
    protected vfsStreamDirectory $realRoot;
    protected vfsStreamDirectory $virtualRoot;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->container = require __DIR__ . '/../config/container.php';

        $this->fixturesRoot = __DIR__ . '/fixtures';

        // Generate phisical files.
        $generator = new Generator();
        $generator->generate();

        $this->virtualRoot = vfsStream::setup('virtual');

        $structure = [];
        $fixtures = [
            'encoding.php',
            'eol.php',
            'extension.php',
            'finalEol.php',
            'indentation.php',
            'leadingEol.php',
            'middleEol.php',
            'miscellaneous.php',
            'name.php',
            'permissions.php',
            'spaceBeforeTab.php',
            'trailingWhitespace.php',
        ];

        $append = function ($fixture) {
            return require $this->fixturesRoot . '/' . $fixture;
        };

        foreach ($fixtures as $fixture) {
            $structure = array_merge_recursive($structure, $append($fixture));
        }

        vfsStream::create($structure);
    }

    /**
     * Create a normalization filter for using in tests.
     */
    protected function createFilter(string $type): NormalizationFilterInterface
    {
        /** @var FilterFactory */
        $factory = $this->container->get(FilterFactory::class);

        return $factory->make($type);
    }

    /**
     * @param array<mixed> $configuration
     */
    protected function createNormalization(string $type, array $configuration = []): NormalizationInterface
    {
        /** @var NormalizationFactory */
        $factory = $this->container->get(NormalizationFactory::class);

        return $factory->make($type, $configuration);
    }
}
