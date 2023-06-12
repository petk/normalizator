<?php

declare(strict_types=1);

namespace Normalizator\Finder;

class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    public function current(): File
    {
        $current = parent::current();
        $rootPath = str_replace($this->getSubPathname(), '', $current->getPathname());

        return new File($current->getPathname(), $this->getSubPathname(), $rootPath);
    }
}
