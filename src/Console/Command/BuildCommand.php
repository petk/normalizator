<?php

declare(strict_types=1);

namespace Normalizator\Console\Command;

use Normalizator\Normalizator;
use Normalizator\Util\PharBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'build',
    description: 'Builds Normalizator PHAR file from the source files.',
)]
class BuildCommand extends Command
{
    public function __construct(
        private PharBuilder $builder,
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * Override here to only generate the help copy when used.
     */
    public function getHelp(): string
    {
        return <<<'EOF'
            The <info>%command.name%</info> packages source normalizator files
            to a single PHAR file and build <comment>normalizator.phar</comment>
            file.
            EOF;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '<info>Creating normalizator.phar</info>',
            '<info>==========================</info>',
            '',
        ]);

        $this->builder->build('normalizator.phar', $output);

        $output->writeln([
            '',
            'Created <info>normalizator.phar</info> version <info>' . Normalizator::VERSION . '</info>',
        ]);

        return Command::SUCCESS;
    }
}
