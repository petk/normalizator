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

#[AsCommand(
    name: 'check',
    description: 'Check all files in the given path.',
)]
class CheckCommand extends Command
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
            The <info>%command.name%</info> command checks files with given normalizations.

              <info>%command.full_name%</info> ~/path/to/files

            The <comment>--encoding, -c</comment> option converts file encoding to UTF-8. Files for writing code are usually encoded as UTF-8 or ASCII.

            The <comment>--eol[=EOL], -e [EOL]</comment> option converts the EOL sequence. The default EOL character is determined by Git, otherwise falls back to LF. It can be manually set with the <info>newline</info> value of LF or CRLF.

            The <comment>--final-eol[=NUM], -N [NUM]</comment> option trims redundant final newlines. The value presents the maximum allowed trailing final newlines. Default number of final newlines is 1. It also inserts a final newline at the end of the file if one is missing. Default EOL character is LF. If file has multiple different EOL characters (LF, CRLF, or CR), the prevailing EOL is used.

            The <comment>--middle-eol[=NUM], -m [NUM]</comment> option trims redundant newlines in the middle of the content. The value presents the maximum allowed middle final newlines. Default number of middle newlines is 1.

            The <comment>--trailing-whitespace, -w</comment> option trims all trailing whitespace characters in text files (spaces, tabs, no-break spaces, Mongolian vowel separators, en quads, em quads, en spaces, em spaces, three-per-em spaces, four-per-em spaces, six-per-em spaces, figure spaces, punctuation spaces, thin spaces, hair spaces, narrow no-break spaces, medium mathematical spaces, ideographic spaces, zero width spaces, and zero width no-break spaces).

            The <comment>--extension, -x</comment> option normalizes the file extension.
            For example, filename.JPEG -> filename.jpg.

            The <comment>--name, -a</comment> option transliterates and slugifies special characters in the directory name or file basename (filename part without extension). For example, foo bar.jpg -> foo-bar.jpg.

            EOF;
    }

    protected function configure(): void
    {
        $this->setDefinition([
            new InputArgument('path', InputArgument::REQUIRED, 'Path to check.'),
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
            ['CHECKING ' . $input->getArgument('path')],
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
                $table->setHeaders(['<error>✘</error> <info>' . $file->getSubPathname() . '</info>']);

                foreach ($this->logger->getLogs($file) as $log) {
                    $table->addRow([' - ' . $log]);
                }

                foreach ($this->logger->getErrors($file) as $log) {
                    $table->addRow([' - ' . $log]);
                }

                $table->render();
                $output->writeln('');

                $exitCode = 1;
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

        if (0 < count($this->logger->getAllLogs()) + count($this->logger->getAllErrors())) {
            $formattedBlock = $formatter->formatBlock(
                [sprintf(
                    '%d of %d %s should be fixed.',
                    count($this->logger->getAllLogs()) + count($this->logger->getAllErrors()),
                    count($this->finder),
                    (1 === count($this->finder)) ? 'file' : 'files',
                )],
                'error',
                true
            );

            $output->writeln(['', $formattedBlock]);
        } else {
            $output->writeln(['', sprintf(
                '<info>Checked %d %s. Everything looks good.</info>',
                count($this->finder),
                (1 === count($this->finder)) ? 'file' : 'files',
            )]);
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
}
