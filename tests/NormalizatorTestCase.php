<?php

declare(strict_types=1);

namespace Normalizator\Tests;

use Normalizator\Container;
use Normalizator\Enum\Permissions;
use Normalizator\Filter\NormalizationFilterInterface;
use Normalizator\FilterFactory;
use Normalizator\Normalization\NormalizationInterface;
use Normalizator\NormalizationFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class NormalizatorTestCase extends TestCase
{
    protected Container $container;
    protected string $fixturesRoot;
    protected vfsStreamDirectory $realRoot;
    protected vfsStreamDirectory $virtualRoot;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
        $this->container = require __DIR__ . '/../config/container.php';

        $this->fixturesRoot = __DIR__ . '/fixtures';

        //$this->realRoot = vfsStream::setup('real');
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
            'name.php',
            'permissions.php',
            'spaceBeforeTab.php',
            'trailingWhitespace.php',
        ];

        $append = function($fixture) {
            return require $this->fixturesRoot . '/' . $fixture;
        };

        foreach ($fixtures as $fixture) {
            $structure = array_merge_recursive($structure, $append($fixture));
        }

        vfsStream::create($structure);

        //vfsStream::copyFromFileSystem($this->fixturesRoot, $this->realRoot);

        // Set some permissions on some files.
        /*$file = $this->realRoot->getChild('initial/permissions/Rakefile');
        $file->chmod(Permissions::FILE->get());
        $file = $this->realRoot->getChild('initial/permissions/not-a-script.sh');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->realRoot->getChild('initial/permissions/shell-script');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->realRoot->getChild('initial/permissions/shell-script-2');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->realRoot->getChild('initial/permissions/shell-script-3');
        $file->chmod(Permissions::EXECUTABLE->get());
        */
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
