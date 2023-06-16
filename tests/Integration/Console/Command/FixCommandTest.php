<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration\Console\Command;

use Normalizator\Cache\Cache;
use Normalizator\ConfigurationResolver;
use Normalizator\Console\Application;
use Normalizator\Console\Command\FixCommand;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\EventDispatcher\Listener\NormalizationListener;
use Normalizator\EventDispatcher\ListenerProvider;
use Normalizator\FilterFactory;
use Normalizator\Finder\File;
use Normalizator\Finder\Finder;
use Normalizator\NormalizationFactory;
use Normalizator\Normalizator;
use Normalizator\Tests\NormalizatorTestCase;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Logger;
use Normalizator\Util\Slugify;
use Normalizator\Util\Timer;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 */
class FixCommandTest extends NormalizatorTestCase
{
    public function createApplication(): Application
    {
        $finder = new Finder();
        $gitDiscovery = new GitDiscovery();
        $eolDiscovery = new EolDiscovery($gitDiscovery);
        $slugify = new Slugify();
        $filenameResolver = new FilenameResolver();
        $cache = new Cache();
        $filterFactory = new FilterFactory($finder, $cache, $gitDiscovery);

        $logger = new Logger();
        $normalizationListener = new NormalizationListener($logger);
        $listenerProvider = new ListenerProvider();
        $listenerProvider->addListener(NormalizationEvent::class, $normalizationListener);
        $eventDispatcher = new EventDispatcher($listenerProvider);

        $normalizationFactory = new NormalizationFactory(
            $finder,
            $slugify,
            $eolDiscovery,
            $gitDiscovery,
            $filterFactory,
            $eventDispatcher,
        );
        $normalizator = new Normalizator($normalizationFactory, $filenameResolver, $eventDispatcher, $logger);
        $configurationResolver = new ConfigurationResolver($eolDiscovery);

        $application = new Application();
        $timer = new Timer();
        $application->add(new FixCommand(
            $configurationResolver,
            $finder,
            $normalizator,
            $timer,
            $logger
        ));

        return $application;
    }

    public function testExecute(): void
    {
        $application = $this->createApplication();

        $command = $application->find('fix');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            // Pass arguments to the helper.
            'path' => 'vfs://' . $this->root->getChild('initial')->path(),
            '--no-interaction' => true,
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('FIXING ', $output);

        $commandTester->execute([
            // Pass arguments to the helper.
            'path' => 'vfs://' . $this->root->getChild('initial/trailing-whitespace')->path(),
        ]);

        $commandTester->assertCommandIsSuccessful();
    }

    public function testExecuteName(): void
    {
        $application = $this->createApplication();

        $command = $application->find('fix');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            // Pass arguments to the helper.
            'path' => 'vfs://' . $this->root->getChild('initial/extensions/files-with-duplicates-after-rename')->path(),
            // Pass options to the helper.
            '--extension' => true,
            '--name' => true,
            '--no-interaction' => true,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('FIXING ', $output);
        $commandTester->assertCommandIsSuccessful();

        $array = [
            'foo bar.txt' => 'foo-bar_2.txt',
            'foo bar.TXT' => 'foo-bar_3.txt',
            'foo--bar.TXT' => 'foo-bar_4.txt',
            'foo-bar_1.txt' => 'foo-bar_1.txt',
            'foo-bar.txt' => 'foo-bar.txt',
            'foo-bar.TXt' => 'foo-bar_5.txt',
            'foo-bar.TXT' => 'foo-bar_6.txt',
        ];

        foreach ($array as $path => $valid) {
            $file = new File('vfs://' . $this->root->getChild('initial/extensions/files-with-duplicates-after-rename/' . $valid)->path());

            $this->assertFileExists($file->getPathname());
        }
    }
}
