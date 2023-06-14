<?php

declare(strict_types=1);

namespace Normalizator\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Normalizator\chmod;
use function Normalizator\copy;
use function Normalizator\md5_file;
use function Normalizator\rename;
use function Normalizator\unlink;

#[AsCommand(
    name: 'self-update',
    description: 'Update normalizator to the latest version.',
)]
class SelfUpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setHelp('This updates normalizator PHAR executable to the latest version.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $remoteFilename = 'https://github.com/petk/normalizator/releases/latest/download/normalizator.phar';
        $localFilename = $_SERVER['argv'][0];
        $tempFilename = basename($localFilename, '.phar').'-temp.phar';

        try {
            copy($remoteFilename, $tempFilename);

            if (md5_file($localFilename) === md5_file($tempFilename)) {
                $output->writeln('<info>normalizator is already at the latest version.</info>');
                unlink($tempFilename);

                return Command::SUCCESS;
            }

            chmod($tempFilename, 0777 & ~umask());

            // Check if Phar is valid.
            $phar = new \Phar($tempFilename);

            // Free the variable to unlock the file.
            unset($phar);

            rename($tempFilename, $localFilename);

            $output->writeln('<info>normalizator updated.</info>');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
                throw $e;
            }

            unlink($tempFilename);

            $output->writeln('<error>The download is corrupt ('.$e->getMessage().').</error>');
            $output->writeln('<error>Please re-run the self-update command to try again.</error>');

            return Command::FAILURE;
        }
    }
}
