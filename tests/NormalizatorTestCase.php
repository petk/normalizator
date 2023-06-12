<?php

declare(strict_types=1);

namespace Normalizator\Tests;

use Normalizator\Enum\Permissions;
use Normalizator\Filter\NormalizationFilterInterface;
use Normalizator\FilterFactory;
use Normalizator\Finder\Finder;
use Normalizator\Normalization\NormalizationInterface;
use Normalizator\NormalizationFactory;
use Normalizator\Observer\NormalizationObserver;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Slugify;
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
    protected string $fixturesRoot;
    protected vfsStreamDirectory $root;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
        $this->fixturesRoot = __DIR__ . '/fixtures';

        $this->root = vfsStream::setup('tests');
        vfsStream::copyFromFileSystem(__DIR__ . '/fixtures');

        // Set some permissions on some files.
        $file = $this->root->getChild('initial/permissions/Rakefile');
        $file->chmod(Permissions::FILE->get());
        $file = $this->root->getChild('initial/permissions/not-a-script.sh');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->root->getChild('initial/permissions/shell-script');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->root->getChild('initial/permissions/shell-script-2');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->root->getChild('initial/permissions/shell-script-3');
        $file->chmod(Permissions::EXECUTABLE->get());
    }

    /**
     * Create a normalization filter for using in tests.
     */
    protected function createFilter(string $type): NormalizationFilterInterface
    {
        $finder = new Finder();
        $gitDiscovery = new GitDiscovery();
        $filterFactory = new FilterFactory($finder, $gitDiscovery);

        return $filterFactory->make($type);
    }

    /**
     * @param array<mixed> $configuration
     */
    protected function createNormalization(string $type, array $configuration = []): NormalizationInterface
    {
        $finder = new Finder();
        $gitDiscovery = new GitDiscovery();
        $slugify = new Slugify();
        $eolDiscovery = new EolDiscovery($gitDiscovery);
        $filterFactory = new FilterFactory($finder, $gitDiscovery);
        $normalizationObserver = new NormalizationObserver();
        $filenameResolver = new FilenameResolver();
        $factory = new NormalizationFactory(
            $finder,
            $slugify,
            $eolDiscovery,
            $gitDiscovery,
            $filenameResolver,
            $filterFactory,
            $normalizationObserver
        );

        return $factory->make($type, $configuration);
    }
}
