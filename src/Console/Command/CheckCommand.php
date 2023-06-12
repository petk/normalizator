<?php

declare(strict_types=1);

namespace Normalizator\Console\Command;

use Normalizator\ConfigurationResolver;
use Normalizator\Finder\Finder;
use Normalizator\Normalizator;
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

            The <comment>--encoding|-c</comment> option converts file encoding to UTF-8. Files for writing code are usually encoded as UTF-8 or ASCII.

            The <comment>--eol[=EOL]|-e [EOL]</comment> option converts the EOL sequence. The default EOL character is determined by Git, otherwise falls back to LF. It can be manually set with the <info>newline</info> value of LF or CRLF.

            The <comment>--final-eol[=NUM]|-N [NUM]</comment> option trims redundant final newlines. The value presents the maximum allowed trailing final newlines. Default number of final newlines is 1. It also inserts a final newline at the end of the file if one is missing. Default EOL character is LF. If file has multiple different EOL characters (LF, CRLF, or CR), the prevailing EOL is used.

            The <comment>--middle-eol[=NUM]|-m [NUM]</comment> option trims redundant newlines in the middle of the content. The value presents the maximum allowed middle final newlines. Default number of middle newlines is 1.
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

            if ($this->normalizator->isNormalized()) {
                $table->setHeaders(['<error>✘</error> <info>' . $file->getSubPathname() . '</info>']);

                foreach ($this->normalizator->getReports() as $report) {
                    $table->addRow([' - ' . $report]);
                }

                foreach ($this->normalizator->getReportsWithManuals() as $report) {
                    $table->addRow([' - ' . $report]);
                }

                $table->render();
                $output->writeln('');

                $exitCode = 1;
            } elseif ($output->isVerbose()) {
                $table->setHeaders(['<info>✔ ' . $file->getSubPathname() . '</info>']);
                $table->render();
            }
        }

        if (1 === $exitCode) {
            $formattedBlock = $formatter->formatBlock(
                ['Files need to be fixed.'],
                'error',
                true
            );

            $output->writeln(['', $formattedBlock]);

            return Command::FAILURE;
        }

        $output->writeln(['', '<info>All files look ok.</info>']);

        return Command::SUCCESS;
    }
}
