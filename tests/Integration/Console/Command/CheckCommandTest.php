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
    public function testExecute(): void
    {
        /** @var CheckCommand */
        $checkCommand = $this->container->get(CheckCommand::class);

        $application = new Application();
        $application->add($checkCommand);

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
