<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration\Console\Command;

use Normalizator\Console\Application;
use Normalizator\Console\Command\CheckCommand;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
#[CoversNothing()]
final class CheckCommandTest extends NormalizatorTestCase
{
    public function testExecute(): void
    {
        $application = $this->createApplication();

        $command = $application->find('check');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial')->path()],
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('CHECKING', $output);

        // Single file.
        $commandTester->execute([
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial/trailing-whitespace/fileWithTrailingWhitespace.php')->path()],
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('CHECKING', $output);
        $this->assertStringContainsString('trailing whitespace', $output);

        // Multiple paths.
        $commandTester->execute([
            'paths' => [
                'vfs://' . $this->virtualRoot->getChild('initial/trailing-whitespace/fileWithTrailingWhitespace.php')->path(),
                'vfs://' . $this->virtualRoot->getChild('initial/final-eol')->path(),
                'vfs://' . $this->virtualRoot->getChild('initial/leading-eol')->path(),
            ],
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('CHECKING ', $output);
        $this->assertStringContainsString('trailing whitespace', $output);
    }

    public function testNotOption(): void
    {
        $application = $this->createApplication();

        $command = $application->find('check');
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

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('CHECKING', $output);
        $this->assertStringNotContainsString('virtual/initial/extension-duplicates-after-rename', $output);
    }

    public function testLeadingEol(): void
    {
        $application = $this->createApplication();

        $command = $application->find('check');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial/leading-eol/file_1.txt')->path()],
            '--leading-eol' => true,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('- 1 leading EOL ', $output);

        $commandTester->execute([
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial/leading-eol/file_5.txt')->path()],
            '--leading-eol' => true,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('- 3 leading EOLs', $output);

        $commandTester->execute([
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial/leading-eol/file_6.txt')->path()],
            '--leading-eol' => true,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('- 4 leading EOLs', $output);
    }

    public function testFinalEol(): void
    {
        $application = $this->createApplication();

        $command = $application->find('check');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial/final-eol/file_5.txt')->path()],
            '--final-eol' => null,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('- 3 final EOLs', $output);

        // Check file by setting the max final EOLS to 3.
        $commandTester->execute([
            'paths' => ['vfs://' . $this->virtualRoot->getChild('initial/final-eol/file_5.txt')->path()],
            '--final-eol' => 3,
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Everything looks good.', $output);
    }

    private function createApplication(): Application
    {
        $application = new Application();

        /** @var CheckCommand */
        $command = $this->container->get(CheckCommand::class);

        $application->add($command);

        return $application;
    }
}
