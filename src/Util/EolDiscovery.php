<?php

declare(strict_types=1);

namespace Normalizator\Util;

use Normalizator\EventDispatcher\Event\DebugEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Finder\File;

use function Normalizator\exec;
use function Normalizator\preg_match;
use function Normalizator\preg_match_all;

/**
 * EOL discovery utility.
 */
class EolDiscovery
{
    /**
     * Default EOL is *nix style LF (\n) character.
     */
    public const DEFAULT_EOL = "\n";

    /**
     * Manually set EOL character from the command line option.
     */
    private string $eol;

    /**
     * @var array<int,string>
     */
    private array $crlfFiles;

    /**
     * Class constructor.
     */
    public function __construct(
        private EventDispatcher $eventDispatcher,
        private GitDiscovery $gitDiscovery
    ) {
    }

    /**
     * Set EOL character to use when inserting new final newline otherwise the
     * EOL will be determined automatically.
     */
    public function setEol(string $eol): void
    {
        $this->eol = $eol;
    }

    /**
     * Get EOL based on the prevailing LF, CRLF or CR newline characters.
     */
    public function getPrevailingEol(string $content): string
    {
        // Empty files don't need a newline attached
        if ('' === $content) {
            return '';
        }

        // Match all LF, CRLF and CR EOL characters
        preg_match_all('/(*BSR_ANYCRLF)\R/', $content, $matches);

        // For single line files the default EOL is returned
        if (is_array($matches[0])) {
            $counts = array_count_values($matches[0]);
            arsort($counts);

            return (string) key($counts);
        }

        return $this->getDefaultEol();
    }

    /**
     * Get the default EOL character by checking a value of command line option --eol,
     * otherwise use Git configuration. When Git is not used, the default EOL is LF.
     */
    public function getDefaultEol(?File $file = null): string
    {
        if (isset($this->eol)) {
            return $this->eol;
        }

        if (null === $file || !$this->gitDiscovery->hasGit($file->getRootPath())) {
            return self::DEFAULT_EOL;
        }

        if (in_array($file->getSubPathname(), $this->getCrlfFiles($file->getRootPath()), true)) {
            return "\r\n";
        }

        return self::DEFAULT_EOL;
    }

    /**
     * Files with eol=crlf Git attribute should have CRLF line endings, others LF.
     *
     * @return array<int,string>
     */
    private function getCrlfFiles(string $path): array
    {
        if (isset($this->crlfFiles)) {
            return $this->crlfFiles;
        }

        exec(sprintf('cd %s && git ls-files -z --eol 2>&1', escapeshellarg($path)), $output, $result);

        $output = implode('', $output);

        if (0 !== $result) {
            $this->eventDispatcher->dispatch(new DebugEvent('Issue with getting CRLF files from Git. Command returned ' . $result . 'Output: ' . $output));
        }

        $files = explode("\0", trim($output ?: ''));
        $files = array_filter($files, function ($item) {
            return preg_match('/^i\/crlf.*[ ]+w\/.*attr\/.*eol=crlf.*$/', $item);
        });

        $this->crlfFiles = preg_filter('/^i\/.*w\/.*attr\/.*[ \t]+/', '', $files);

        return $this->crlfFiles;
    }
}
