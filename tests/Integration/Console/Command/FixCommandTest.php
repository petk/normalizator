<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration\Console\Command;

use Normalizator\Console\Application;
use Normalizator\Console\Command\FixCommand;
use Normalizator\Finder\File;
use Normalizator\Tests\NormalizatorTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 */
class FixCommandTest extends NormalizatorTestCase
{
    public function testExecute(): void
    {
        $application = $this->createApplication();

        $command = $application->find('fix');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            // Pass arguments to the helper.
            'paths' => ['vfs://' . $this->root->getChild('initial')->path()],
            '--no-interaction' => true,
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('FIXING ', $output);

        $commandTester->execute([
            // Pass arguments to the helper.
            'paths' => [
                'vfs://' . $this->root->getChild('initial/trailing-whitespace')->path(),
                'vfs://' . $this->root->getChild('initial/leading-eol')->path(),
            ],
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
            'paths' => ['vfs://' . $this->root->getChild('initial/extensions/files-with-duplicates-after-rename')->path()],
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

    private function createApplication(): Application
    {
        $application = new Application();

        /** @var FixCommand */
        $command = $this->container->get(FixCommand::class);

        $application->add($command);

        return $application;
    }
}
