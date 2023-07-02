<?php

declare(strict_types=1);

namespace Normalizator\Console\Command;

use Exception;
use Phar;
use PharException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

use function basename;
use function is_array;
use function is_string;
use function Normalizator\chmod;
use function Normalizator\copy;
use function Normalizator\md5_file;
use function Normalizator\rename;
use function Normalizator\unlink;
use function umask;

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

    /**
     * @throws UnexpectedValueException
     * @throws PharException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ('' === Phar::running()) {
            $output->writeln('<error>The self-update command can be only executed when running normalizator.phar.</error>');

            return Command::FAILURE;
        }

        if (is_array($_SERVER['argv']) && is_string($_SERVER['argv'][0])) {
            $localFilename = $_SERVER['argv'][0];
        } else {
            $output->writeln('<error>Could not get filename from the arguments passed to script.');

            return Command::FAILURE;
        }

        try {
            $tempFilename = basename($localFilename, '.phar') . '-temp.phar';

            $remoteFilename = 'https://github.com/petk/normalizator/releases/latest/download/normalizator.phar';
            copy($remoteFilename, $tempFilename);

            if (md5_file($localFilename) === md5_file($tempFilename)) {
                $output->writeln('<info>normalizator is already at the latest version.</info>');
                unlink($tempFilename);

                return Command::SUCCESS;
            }

            chmod($tempFilename, 0777 & ~umask());

            // Check if Phar is valid.
            $phar = new Phar($tempFilename);

            // Free the variable to unlock the file.
            unset($phar);

            rename($tempFilename, $localFilename);

            $output->writeln('<info>normalizator updated.</info>');

            return Command::SUCCESS;
        } catch (Exception $e) {
            if (!$e instanceof UnexpectedValueException && !$e instanceof PharException) {
                throw $e;
            }

            unlink($tempFilename);

            $output->writeln('<error>The download is corrupt (' . $e->getMessage() . ').</error>');
            $output->writeln('<error>Please re-run the self-update command to try again.</error>');

            return Command::FAILURE;
        }
    }
}
