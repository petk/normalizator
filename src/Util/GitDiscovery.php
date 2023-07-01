<?php

declare(strict_types=1);

namespace Normalizator\Util;

use Normalizator\Cache\Cache;
use Normalizator\Finder\File;

use function Normalizator\exec;
use function Normalizator\preg_match;

/**
 * Helper for checking if given path is a Git repository or is in the Git
 * repository, and if Git is present on the current system.
 */
class GitDiscovery
{
    public function __construct(private Cache $cache)
    {
    }

    /**
     * Determine if the given path is Git repository.
     */
    public function hasGit(string $path): bool
    {
        $key = static::class . ':' . $path;

        if ($this->cache->has($key) && \is_bool($this->cache->get($key))) {
            return $this->cache->get($key);
        }

        $hasGit = $this->checkGit($path);

        $this->cache->set($key, $hasGit);

        return $hasGit;
    }

    /**
     * Check if given file is inside Git directory.
     */
    public function isInGit(File $file): bool
    {
        // Check if given file is within the .git directory.
        if (1 === preg_match('/(^|\/)\.git(|\/.+)$/', $file->getPathname())) {
            return true;
        }

        // Check if given file is within a bare Git directory.
        if ($this->isInBareGitRepo($file)) {
            return true;
        }

        return false;
    }

    public function isBareGitRepo(string $file): bool
    {
        $pattern = $file . '/branches';
        if (!is_dir($pattern)) {
            return false;
        }

        $pattern = $file . '/config';
        if (!is_file($pattern)) {
            return false;
        }

        $pattern = $file . '/HEAD';
        if (!is_file($pattern)) {
            return false;
        }

        $pattern = $file . '/hooks';
        if (!is_dir($pattern)) {
            return false;
        }

        $pattern = $file . '/info';
        if (!is_dir($pattern)) {
            return false;
        }

        $pattern = $file . '/objects';
        if (!is_dir($pattern)) {
            return false;
        }

        $pattern = $file . '/refs';
        if (!is_dir($pattern)) {
            return false;
        }

        return true;
    }

    private function checkGit(string $path): bool
    {
        exec('git --version 2>&1', $output, $exitCode);

        if (0 !== $exitCode) {
            return false;
        }

        if (is_dir($path) && file_exists($path . '/.git')) {
            return true;
        }

        return false;
    }

    /**
     * Checks if given file is within a bare Git repository.
     *
     * In case Finder was used, the root path of the Finder is used as the
     * ending checkpoint otherwise the ending checkpoint is given file's parent
     * path.
     */
    private function isInBareGitRepo(File $file): bool
    {
        $current = $file;

        while (
            $file->getRootPath() !== $current->getPathname()
            && '' !== $current->getPathname()
            && file_exists($current->getPathname())
        ) {
            if ($this->isBareGitRepo($current->getPathname())) {
                return true;
            }

            $current = new File($current->getPath());
        }

        return false;
    }
}
