<?php

declare(strict_types=1);

namespace Normalizator\Console\Command;

use Normalizator\ConfigurationResolver;
use Normalizator\Finder\Finder;
use Normalizator\Normalizator;
use Normalizator\Util\Logger;
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
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
        private Logger $logger,
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

            It accepts the same options as the <comment>check</comment> command.

            Prior to running this command, a good idea is to first check what will be fixed with the <comment>check</comment> command.
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
            new InputOption('extension', 'x', InputOption::VALUE_NONE, 'Fix file extensions.'),
            new InputOption('name', 'a', InputOption::VALUE_NONE, 'Fix file and directory names.'),
            new InputOption('permissions', 'u', InputOption::VALUE_NONE, 'Fix file and directory permissions.'),
            new InputOption('space-before-tab', 's', InputOption::VALUE_NONE, 'Clean spaces before tabs in the initial part of the line.'),
            new InputOption('trailing-whitespace', 'w', InputOption::VALUE_NONE, 'Trim trailing whitespace characters.'),
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

        /** @var \Symfony\Component\Console\Helper\FormatterHelper */
        $formatter = $this->getHelper('formatter');

        $formattedBlock = $formatter->formatBlock(
            ['FIXING ' . $input->getArgument('path')],
            'header',
            true
        );

        $output->writeln([$formattedBlock, '']);

        if (!$this->askForConfirmation($input, $output)) {
            return Command::SUCCESS;
        }

        $output->writeln(['', 'Fixing files in progress.']);

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

                if ([] !== $this->logger->getErrors($file)) {
                    $table->setHeaders(['<info>🔧 ' . $file->getSubPathname() . '</info>']);
                } else {
                    $table->setHeaders(['<info>✔ ' . $file->getSubPathname() . '</info>']);
                }

                foreach ($this->logger->getLogs($file) as $log) {
                    $table->addRow([' <info>✔</info> ' . $log]);
                }

                foreach ($this->logger->getErrors($file) as $log) {
                    $exitCode = 1;
                    $table->addRow([' <error>✘</error> ' . $log]);
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

        if (0 < count($this->logger->getAllLogs())) {
            $output->writeln(['', sprintf(
                '<info>%d %s been fixed; Checked %d %s.</info>',
                count($this->logger->getAllLogs()),
                (1 === count($this->logger->getAllLogs())) ? 'file has' : 'files have',
                count($this->finder),
                (1 === count($this->finder)) ? 'file' : 'files',
            )]);
        }

        if (0 < count($this->logger->getAllErrors())) {
            $formattedBlock = $formatter->formatBlock(
                [sprintf(
                    '%d %s should be fixed manually.',
                    count($this->logger->getAllErrors()),
                    (1 === count($this->logger->getAllErrors())) ? 'file' : 'files',
                )],
                'error',
                true
            );

            $output->writeln(['', $formattedBlock]);

            $exitCode = 1;
        }

        // Print debug messages.
        if (0 < count($this->logger->getDebugMessages())) {
            if ($output->isDebug()) {
                $output->writeln([
                    '',
                    '<error>Debug errors and warnings:</error>',
                    '  - ' . implode("\n  - ", $this->logger->getDebugMessages()),
                ]);
            }

            $exitCode = 1;
        }

        return (1 === $exitCode) ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Returns true if user confirms to continue, false otherwise.
     */
    private function askForConfirmation(InputInterface $input, OutputInterface $output): bool
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper */
        $helper = $this->getHelper('question');

        $noInteraction = (true === $input->getOption('no-interaction')) ? true : false;
        $question = new ConfirmationQuestion('Files in given path will be overwriten. Do you want to continue? <comment>[N/y]</comment> ', $noInteraction, '/^(y|yes|1)$/i');

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln(['', 'Exiting without fixing files.']);

            return false;
        }

        return true;
    }
}
