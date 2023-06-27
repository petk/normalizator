<?php

declare(strict_types=1);

namespace Normalizator\Tests;

use Normalizator\Enum\Permissions;

use function Normalizator\chmod;
use function Normalizator\file_put_contents;
use function Normalizator\mkdir;
use function Normalizator\rmdir;

/**
 * Generates fixtures.
 */
class Generator
{
    private string $root = __DIR__ . '/fixtures/generated';

    public function generate(): void
    {
        // Clean any previous generated directory.
        $directory = $this->root . '/initial';

        $this->rm($directory);

        $permissions = require __DIR__ . '/fixtures/permissions.php';

        mkdir($this->root . '/initial');
        mkdir($this->root . '/initial/permissions');

        foreach ($permissions['initial']['permissions'] as $file => $content) {
            file_put_contents($this->root . '/initial/permissions/' . $file, $content);
        }

        chmod($this->root . '/initial/permissions/not-a-script.sh', Permissions::EXECUTABLE->get());
        chmod($this->root . '/initial/permissions/shell-script', Permissions::EXECUTABLE->get());
        chmod($this->root . '/initial/permissions/shell-script_2', Permissions::EXECUTABLE->get());
        chmod($this->root . '/initial/permissions/shell-script_3', Permissions::EXECUTABLE->get());
    }

    /**
     * Recursively remove directory.
     */
    private function rm(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        /**
         * @var iterable<string,\SplFileInfo>
         */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $action($fileinfo->getRealPath());
        }

        rmdir($directory);
    }
}
