<?php

declare(strict_types=1);

namespace Normalizator\Tests\Console\Command;

use Normalizator\ConfigurationResolver;
use Normalizator\Console\Application;
use Normalizator\Console\Command\CheckCommand;
use Normalizator\FilterFactory;
use Normalizator\Finder\Finder;
use Normalizator\NormalizationFactory;
use Normalizator\Normalizator;
use Normalizator\Observer\NormalizationObserver;
use Normalizator\Tests\NormalizatorTestCase;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Slugify;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 */
class CheckCommandTest extends NormalizatorTestCase
{
    public function testExecute(): void
    {
        $finder = new Finder();
        $gitDiscovery = new GitDiscovery();
        $eolDiscovery = new EolDiscovery($gitDiscovery);
        $slugify = new Slugify();
        $filenameResolver = new FilenameResolver();
        $normalizationObserver = new NormalizationObserver();
        $filterFactory = new FilterFactory($finder, $gitDiscovery);
        $normalizationFactory = new NormalizationFactory(
            $finder,
            $slugify,
            $eolDiscovery,
            $gitDiscovery,
            $filenameResolver,
            $filterFactory,
            $normalizationObserver
        );
        $normalizator = new Normalizator($normalizationFactory, $normalizationObserver);
        $configurationResolver = new ConfigurationResolver($eolDiscovery);

        $application = new Application();
        $application->add(new CheckCommand($configurationResolver, $finder, $normalizator));

        $command = $application->find('check');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'path' => 'vfs://' . $this->root->getChild('initial')->path(),
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('CHECKING ', $output);

        // Single file.
        $commandTester->execute([
            'path' => 'vfs://' . $this->root->getChild('initial/trailing-whitespace/fileWithTrailingSpaces.php')->path(),
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('CHECKING ', $output);
        $this->assertStringContainsString('trailing whitespace', $output);
    }
}
