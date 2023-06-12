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
    name: 'fix',
    description: 'Fix all files in the given path.',
)]
class FixCommand extends Command
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

            if ($this->normalizator->isNormalized()) {
                $this->normalizator->save($file);

                if ([] !== $this->normalizator->getReportsWithManuals()) {
                    $table->setHeaders(['<info>🔧 ' . $file->getSubPathname() . '</info>']);
                } else {
                    $table->setHeaders(['<info>✔ ' . $file->getSubPathname()]);
                }

                foreach ($this->normalizator->getReports() as $report) {
                    $table->addRow([' <info>✔</info> ' . $report]);
                }

                foreach ($this->normalizator->getReportsWithManuals() as $report) {
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

        if (1 === $exitCode) {
            $formattedBlock = $formatter->formatBlock(
                ['Some files need to be fixed manually.'],
                'error',
                true
            );

            $output->writeln(['', $formattedBlock]);

            return Command::FAILURE;
        }

        $output->writeln(['', '<info>Files have been fixed.</info>']);

        return Command::SUCCESS;
    }
}
