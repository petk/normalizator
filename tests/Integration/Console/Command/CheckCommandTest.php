<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration\Console\Command;

use Normalizator\Console\Application;
use Normalizator\Console\Command\CheckCommand;
use Normalizator\Tests\NormalizatorTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 */
class CheckCommandTest extends NormalizatorTestCase
{
    private function createApplication(): Application
    {
        $application = new Application();

        /** @var CheckCommand */
        $command = $this->container->get(CheckCommand::class);

        $application->add($command);

        return $application;
    }

    public function testExecute(): void
    {
        $application = $this->createApplication();

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

    public function testLeadingEol(): void
    {
        $application = $this->createApplication();

        $command = $application->find('check');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'path' => 'vfs://' . $this->root->getChild('initial/leading-eol/file-5.txt')->path(),
            '--leading-eol' => true,
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('- 3 leading EOL(s)', $output);

        $commandTester->execute([
            'path' => 'vfs://' . $this->root->getChild('initial/leading-eol/file-6.txt')->path(),
            '--leading-eol' => true,
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('- 4 leading EOL(s)', $output);
    }
}
