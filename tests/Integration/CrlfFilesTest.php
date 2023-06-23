<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration;

use Normalizator\Console\Application;
use Normalizator\Console\Command\FixCommand;
use Normalizator\Tests\NormalizatorTestCase;
use Symfony\Component\Console\Tester\CommandTester;

use function Normalizator\exec;
use function Normalizator\file_put_contents;
use function Normalizator\mkdir;
use function Normalizator\rmdir;

/**
 * @internal
 *
 * @coversNothing
 */
class CrlfFilesTest extends NormalizatorTestCase
{
    /**
     * Test CRLF files in the Git repository.
     */
    public function testCrlfFiles(): void
    {
        $repo = $this->fixturesRoot . '/generated/git-repo-with-crlf';

        // Clean any previous Git repo.
        if (is_dir($repo)) {
            /**
             * @var iterable<string,\SplFileInfo>
             */
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($repo, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            rmdir($repo);
        }

        // Create fresh Git repo.
        mkdir($repo);
        file_put_contents($repo . '/README.md', "# README\r\n\r\nLorem ipsum\r\ndolor\r\nsit\r\namet\r\n");
        file_put_contents($repo . '/.gitattributes', "README.md eol=crlf\n");

        exec(sprintf('cd %s && git init 2>&1 && git add -A && git commit --author="Tester <tester@localhost>" -m "Initial commit"', escapeshellarg($repo)), $output, $result);

        /** @var FixCommand */
        $fixCommand = $this->container->get(FixCommand::class);

        $application = new Application();

        $application->add($fixCommand);

        $command = $application->find('fix');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            // Pass arguments to the helper.
            'paths' => [$repo],
            '--no-interaction' => true,
            '--eol' => null,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('FIXING ', $output);
    }
}
