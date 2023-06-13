<?php

declare(strict_types=1);

namespace Normalizator\Finder;

/**
 * The Finder utility to get files.
 */
class Finder implements \Countable
{
    private \Iterator $iterator;

    public function getTree(string $path, ?callable $filter = null, int $flags = \RecursiveIteratorIterator::CHILD_FIRST): \Iterator
    {
        $path = new File($path);

        // Phar files return false for \SplFileInfo::getRealPath() so we combine
        // here both. Real path is used for cases where paths are given with
        // relative paths /path/to/some/directory/../../some/directory
        $path = $path->getRealPath() ?: $path->getPathname();

        if (is_file($path)) {
            $info = new File($path);

            return new \ArrayIterator([$info->getPathname() => $info]);
        }

        $innerIterator = new RecursiveDirectoryIterator(
            $path,
            RecursiveDirectoryIterator::SKIP_DOTS
        );

        // Set custom \SplFileInfo class.
        $innerIterator->setInfoClass(File::class);

        $filter ??= function () {
            return true;
        };

        return $this->iterator = new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator($innerIterator, $filter),
            $flags
        );
    }

    /**
     * Count the results in the last operation.
     */
    public function count(): int
    {
        if (!isset($this->iterator)) {
            return 0;
        }

        return \iterator_count($this->iterator);
    }
}
