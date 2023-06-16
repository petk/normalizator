<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration\Console\Command;

use Normalizator\Cache\Cache;
use Normalizator\ConfigurationResolver;
use Normalizator\Console\Application;
use Normalizator\Console\Command\CheckCommand;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\EventDispatcher\Listener\NormalizationListener;
use Normalizator\EventDispatcher\ListenerProvider;
use Normalizator\FilterFactory;
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
class CheckCommandTest extends NormalizatorTestCase
{
    public function testExecute(): void
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

        $timer = new Timer();

        $application = new Application();
        $application->add(new CheckCommand(
            $configurationResolver,
            $finder,
            $normalizator,
            $timer,
            $logger,
        ));

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
