<?php

declare(strict_types=1);

namespace Normalizator\Console\Command;

use Normalizator\Configuration\Configuration;
use Normalizator\Configuration\Configurator;
use Normalizator\Finder\File;
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
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'fix',
    description: 'Fix all files in the given path.',
)]
class FixCommand extends Command
{
    private int $exitCode;

    public function __construct(
        private Configurator $configurator,
        private Configuration $configuration,
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

    /**
     * Get manually entered encoding for a given file.
     */
    public function askForEncoding(
        File $file,
        InputInterface $input,
        OutputInterface $output,
        string $defaultEncoding = ''
    ): string {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper */
        $helper = $this->getHelper('question');

        $question = new Question(
            sprintf(
                'Please enter valid encoding for <info>%s</info> <comment>%s</comment> ',
                $file->getPathname(),
                '' !== $defaultEncoding ? '(' . $defaultEncoding . '?)' : ''
            ),
            $defaultEncoding
        );

        /** @var string */
        return $helper->ask($input, $output, $question);
    }

    protected function configure(): void
    {
        $this->setDefinition([
            new InputArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Paths to check.'),
            new InputOption('encoding', 'c', InputOption::VALUE_NONE, 'Convert file encoding to UTF-8.'),
            new InputOption('eol', 'e', InputOption::VALUE_OPTIONAL, 'Convert EOL sequence.', false),
            new InputOption('extension', 'x', InputOption::VALUE_NONE, 'Fix file extensions.'),
            new InputOption('final-eol', 'N', InputOption::VALUE_OPTIONAL, 'Trim redundant final EOLs.', false),
            new InputOption('indentation', 'i', InputOption::VALUE_OPTIONAL, 'Normalize indentation style.', false),
            new InputOption('indentation-size', null, InputOption::VALUE_REQUIRED, 'Set indentation size.', false),
            new InputOption('leading-eol', 'l', InputOption::VALUE_NONE, 'Trim redundant leading newlines.'),
            new InputOption('middle-eol', 'm', InputOption::VALUE_OPTIONAL, 'Trim redundant middle empty newlines.', false),
            new InputOption('name', 'a', InputOption::VALUE_NONE, 'Fix file and directory names.'),
            new InputOption('permissions', 'u', InputOption::VALUE_NONE, 'Fix file and directory permissions.'),
            new InputOption('space-before-tab', 's', InputOption::VALUE_NONE, 'Clean spaces before tabs in the initial part of the line.'),
            new InputOption('trailing-whitespace', 'w', InputOption::VALUE_NONE, 'Trim trailing whitespace characters.'),
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->configurator->set($input->getOptions());

            if (true !== $input->getOption('no-interaction')) {
                $this->configuration->set('encoding_callback', function (File $file, string $defaultEncoding = '') use ($input, $output): string {
                    return $this->askForEncoding($file, $input, $output, $defaultEncoding);
                });
            }
        } catch (InvalidOptionException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        /** @var array<int,string> */
        $paths = $input->getArgument('paths');

        $outputStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('header', $outputStyle);

        /** @var \Symfony\Component\Console\Helper\FormatterHelper */
        $formatter = $this->getHelper('formatter');

        $formattedBlock = $formatter->formatBlock(
            ['FIXING', ...$paths],
            'header',
            true
        );

        $output->writeln([$formattedBlock, '']);

        if (!$this->askForConfirmation($input, $output)) {
            return Command::SUCCESS;
        }

        $output->writeln(['', 'Fixing files in progress.']);

        $this->exitCode = 0;

        $count = 0;
        foreach ($paths as $path) {
            $iterator = $this->normalize($path, $output);
            $count += \iterator_count($iterator);
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
                $count,
                (1 === $count) ? 'file' : 'files',
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

            $this->exitCode = 1;
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

            $this->exitCode = 1;
        }

        // Clear logger for clean state.
        $this->logger->clear();

        return (1 === $this->exitCode) ? Command::FAILURE : Command::SUCCESS;
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

    private function normalize(string $path, OutputInterface $output): \Iterator
    {
        $iterator = $this->finder->getTree($path);

        foreach ($iterator as $file) {
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
                    $table->setHeaders(['<info>🔧 ' . $file->getPathname() . '</info>']);
                } else {
                    $table->setHeaders(['<info>✔ ' . $file->getPathname() . '</info>']);
                }

                foreach ($this->logger->getLogs($file) as $log) {
                    $table->addRow([' <info>✔</info> ' . $log]);
                }

                foreach ($this->logger->getErrors($file) as $log) {
                    $this->exitCode = 1;
                    $table->addRow([' <error>✘</error> ' . $log]);
                }

                $table->render();
                $output->writeln('');
            } elseif ($output->isVerbose()) {
                $table->setHeaders(['<info>✔ ' . $file->getPathname() . '</info>']);
                $table->render();
            }
        }

        return $iterator;
    }
}
