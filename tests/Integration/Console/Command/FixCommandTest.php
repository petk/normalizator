<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration\Console\Command;

use Normalizator\Console\Application;
use Normalizator\Console\Command\FixCommand;
use Normalizator\Finder\File;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
#[CoversNothing()]
final class FixCommandTest extends NormalizatorTestCase
{
    public function testExecute(): void
    {
        $application = $this->createApplication();

        $command = $application->find('fix');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            // Pass arguments to the helper.
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial')->path()],
            '--no-interaction' => true,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('FIXING', $output);
    }

    public function testExecuteName(): void
    {
        $application = $this->createApplication();

        $command = $application->find('fix');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            // Pass arguments to the helper.
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial/extension-duplicates-after-rename')->path()],
            // Pass options to the helper.
            '--extension' => true,
            '--name' => true,
            '--no-interaction' => true,
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('FIXING', $output);
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
            $file = new File('vfs://' . $this->virtualRoot->getChild('initial/extension-duplicates-after-rename/' . $valid)->path());

            $this->assertFileExists($file->getPathname());
        }
    }

    public function testNotOption(): void
    {
        $application = $this->createApplication();

        $command = $application->find('fix');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'paths' => [
                'vfs://' . $this->virtualRoot->getChild('initial')->path(),
            ],
            '--not' => [
                'permissions',
                'vfs://' . $this->virtualRoot->getChild('initial/trailing-whitespace')->path(),
                'name',
                'middle-eol',
                'non-existing-for-checking-bypass',
                'middle-eol-2',
                'leading-eol',
                'indentation-2',
                'indentation',
                'final-eol',
                'final-eol-2',
                'extension',
                'eol',
                'miscellaneous',
                'space-before-tab',
                'extension-duplicates',
                'final-eol-crlf',
                'extension-duplicates-after-rename',
            ],
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('FIXING', $output);
        $this->assertStringNotContainsString('virtual/initial/extension-duplicates-after-rename', $output);
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
