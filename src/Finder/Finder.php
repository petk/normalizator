<?php

declare(strict_types=1);

namespace Normalizator\Finder;

use ArrayIterator;
use Iterator;
use RecursiveCallbackFilterIterator;
use RecursiveIteratorIterator;

use function is_file;

/**
 * The Finder utility to get files.
 */
class Finder
{
    /**
     * @return Iterator<string,File>
     */
    public function getTree(string $path, ?callable $filter = null, int $flags = RecursiveIteratorIterator::CHILD_FIRST): Iterator
    {
        $path = new File($path);

        // Phar files return false for \SplFileInfo::getRealPath() so we combine
        // here both. Real path is used for cases where paths are given with
        // relative paths /path/to/some/directory/../../some/directory
        $path = $path->getRealPath() ?: $path->getPathname();

        if (is_file($path)) {
            $info = new File($path);

            return new ArrayIterator([$info->getPathname() => $info]);
        }

        $innerIterator = new RecursiveDirectoryIterator(
            $path,
            RecursiveDirectoryIterator::SKIP_DOTS,
        );

        // Set custom \SplFileInfo class.
        $innerIterator->setInfoClass(File::class);

        $filter ??= static function () {
            return true;
        };

        return new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator($innerIterator, $filter),
            $flags,
        );
    }
}
