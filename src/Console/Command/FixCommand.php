<?php

declare(strict_types=1);

namespace Normalizator\Console\Command;

use Normalizator\ConfigurationResolver;
use Normalizator\Finder\Finder;
use Normalizator\Normalizator;
use Normalizator\Util\Timer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'fix',
    description: 'Fix all files in the given path.',
)]
class FixCommand extends Command
{
    public function __construct(
        private ConfigurationResolver $configurationResolver,
        private Finder $finder,
        private Normalizator $normalizator,
        private Timer $timer,
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
            The <info>%command.name%</info> command fixes files with given normalizations.

            For the options see help of the <comment>check</comment> command. It accepts the same options as the <comment>check</comment> command.

            Prior to running this command, it is good to first check what will be fixed with the <comment>check</comment> command.
            EOF;
    }

    protected function configure(): void
    {
        $this->setDefinition([
            new InputArgument('path', InputArgument::REQUIRED, 'Path to fix.'),
            new InputOption('encoding', 'c', InputOption::VALUE_NONE, 'Convert file encoding to UTF-8.'),
            new InputOption('eol', 'e', InputOption::VALUE_OPTIONAL, 'Convert EOL sequence.', false),
            new InputOption('final-eol', 'N', InputOption::VALUE_OPTIONAL, 'Trim redundant final EOLs.', false),
            new InputOption('leading-eol', 'l', InputOption::VALUE_NONE, 'Trim redundant leading newlines.'),
            new InputOption('middle-eol', 'm', InputOption::VALUE_OPTIONAL, 'Trim redundant middle empty newlines.', false),
            new InputOption('path-name', 'p', InputOption::VALUE_NONE, 'Fix file and directory names.'),
            new InputOption('permissions', 'u', InputOption::VALUE_NONE, 'Fix file and directory permissions.'),
            new InputOption('space-before-tab', 's', InputOption::VALUE_NONE, 'Clean spaces before tabs in the initial part of the line.'),
            new InputOption('trailing-whitespace', 'w', InputOption::VALUE_NONE, 'Trim trailing whitespace (spaces and tabs).'),
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $options = $this->configurationResolver->resolve($input->getOptions());

            foreach ($options as $key => $option) {
                $input->setOption($key, $option);
            }

            $this->normalizator->setOptions($input->getOptions());
        } catch (InvalidOptionException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        $outputStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('header', $outputStyle);

        /**
         * @var \Symfony\Component\Console\Helper\FormatterHelper
         */
        $formatter = $this->getHelper('formatter');

        $formattedBlock = $formatter->formatBlock(
            ['FIXING ' . $input->getArgument('path')],
            'header',
            true
        );

        $output->writeln([$formattedBlock, '']);

        $exitCode = 0;

        foreach ($this->finder->getTree($input->getArgument('path')) as $file) {
            $this->normalizator->normalize($file);

            $tableStyle = new TableStyle();
            $tableStyle->setHorizontalBorderChars('', null);
            $tableStyle->setVerticalBorderChars('', null);
            $tableStyle->setDefaultCrossingChar('');

            $table = new Table($output);
            $table->setStyle($tableStyle);

            if ($this->normalizator->isNormalized($file)) {
                $this->normalizator->save($file);

                if ([] !== $this->normalizator->getObserver()->getErrors($file)) {
                    $table->setHeaders(['<info>🔧 ' . $file->getSubPathname() . '</info>']);
                } else {
                    $table->setHeaders(['<info>✔ ' . $file->getSubPathname() . '</info>']);
                }

                foreach ($this->normalizator->getObserver()->getReports($file) as $report) {
                    $table->addRow([' <info>✔</info> ' . $report]);
                }

                foreach ($this->normalizator->getObserver()->getErrors($file) as $report) {
                    $exitCode = 1;
                    $table->addRow([' <error>✘</error> ' . $report]);
                }

                $table->render();
                $output->writeln('');
            } elseif ($output->isVerbose()) {
                $table->setHeaders(['<info>✔ ' . $file->getSubPathname() . '</info>']);
                $table->render();
            }
        }

        // Script execution info.
        $output->writeln(['', sprintf(
            'Time: %.3f sec; Memory: %.3f MB.',
            $this->timer->stop(),
            round(memory_get_peak_usage() / 1024 / 1024, 3),
        )]);

        if (1 === $exitCode) {
            $output->writeln(['', sprintf(
                '<info>%d %s been fixed; Checked %d %s.</info>',
                count($this->normalizator->getObserver()->getAllReports()),
                (1 === count($this->normalizator->getObserver()->getAllReports())) ? 'file has' : 'files have',
                count($this->finder),
                (1 === count($this->finder)) ? 'file' : 'files',
            )]);

            $formattedBlock = $formatter->formatBlock(
                [sprintf(
                    '%d %s should be fixed manually.',
                    count($this->normalizator->getObserver()->getAllErrors()),
                    (1 === count($this->normalizator->getObserver()->getAllErrors())) ? 'file' : 'files',
                )],
                'error',
                true
            );

            $output->writeln(['', $formattedBlock]);

            return Command::FAILURE;
        }

        $output->writeln(['', sprintf(
            '<info>%d %s been fixed; Checked %d %s.</info>',
            count($this->normalizator->getObserver()->getAllReports()),
            (1 === count($this->normalizator->getObserver()->getAllReports())) ? 'file has' : 'files have',
            count($this->finder),
            (1 === count($this->finder)) ? 'file' : 'files',
        )]);

        return Command::SUCCESS;
    }
}
