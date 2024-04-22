<?php

declare(strict_types=1);

namespace Normalizator\Finder;

use FilesystemIterator;
use RuntimeException;
use SplFileInfo;

use function str_replace;

class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    public function __construct(
        string $directory,
        int $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO,
    ) {
        if ($flags & (self::CURRENT_AS_PATHNAME | self::CURRENT_AS_SELF)) {
            throw new RuntimeException('This iterator only support \FilesystemIterator::CURRENT_AS_FILEINFO flag.');
        }

        parent::__construct($directory, $flags);
    }

    public function current(): File
    {
        /** @var SplFileInfo */
        $current = parent::current();

        $rootPath = str_replace($this->getSubPathname(), '', $current->getPathname());

        return new File($current->getPathname(), $this->getSubPathname(), $rootPath);
    }
}
