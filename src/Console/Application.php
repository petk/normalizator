<?php

declare(strict_types=1);

namespace Normalizator\Console;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;

/**
 * Overridden console application for normalizator.
 */
class Application extends ConsoleApplication
{
    public function getLongVersion(): string
    {
        return implode("\n", [
            parent::getLongVersion(),
            '',
            'Command line tool that checks and fixes trailing whitespace, LF or CRLF newline characters, redundant newlines, permissions and similar in given files.',
        ]);
    }

    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new ListCommand()];
    }
}
