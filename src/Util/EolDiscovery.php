<?php

declare(strict_types=1);

namespace Normalizator\Util;

use Normalizator\Cache\Cache;
use Normalizator\EventDispatcher\Event\DebugEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Finder\File;

use function array_filter;
use function escapeshellarg;
use function explode;
use function implode;
use function in_array;
use function is_array;
use function Normalizator\exec;
use function Normalizator\preg_match;
use function preg_filter;
use function sprintf;
use function trim;

/**
 * EOL discovery utility.
 */
class EolDiscovery
{
    /**
     * Default EOL is *nix style LF (\n) character.
     */
    public const DEFAULT_EOL = "\n";

    public function __construct(
        private EventDispatcher $eventDispatcher,
        private GitDiscovery $gitDiscovery,
        private Cache $cache,
    ) {}

    /**
     * Get the default EOL character by checking a value of command line option
     * --eol option otherwise use Git configuration. When Git is not used, the
     * default EOL is LF.
     */
    public function getEolForFile(File $file, string $defaultEol = self::DEFAULT_EOL): string
    {
        // File has eol=crlf Git attribute.
        if (
            $this->gitDiscovery->hasGit($file->getRootPath())
            && in_array($file->getSubPathname(), $this->getCrlfFiles($file->getRootPath()), true)
        ) {
            return "\r\n";
        }

        return $defaultEol;
    }

    /**
     * Get all files with eol=crlf Git attribute.
     *
     * These files should always have CRLF line endings.
     *
     * @return array<int,string>
     */
    private function getCrlfFiles(string $path): array
    {
        $key = static::class . ':' . $path;

        if ($this->cache->has($key) && is_array($this->cache->get($key))) {
            /** @var array<int,string> */
            return $this->cache->get($key);
        }

        exec(sprintf('cd %s && git ls-files -z --eol 2>&1', escapeshellarg($path)), $output, $result);

        $output = implode('', $output);

        if (0 !== $result) {
            $this->eventDispatcher->dispatch(new DebugEvent('Issue with getting CRLF files from Git. Command returned ' . $result . 'Output: ' . $output));
        }

        $files = explode("\0", trim($output ?: ''));
        $files = array_filter($files, static function ($item) {
            return preg_match('/^i\/crlf.*[ ]+w\/.*attr\/.*eol=crlf.*$/', $item);
        });

        /** @var array<int,string> */
        $crlfFiles = preg_filter('/^i\/.*w\/.*attr\/.*[ \t]+/', '', $files);

        $this->cache->set($key, $crlfFiles);

        return $crlfFiles;
    }
}
