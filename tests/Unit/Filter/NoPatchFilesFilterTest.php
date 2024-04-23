<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Filter;

use Normalizator\Finder\Finder;
use Normalizator\Tests\NormalizatorTestCase;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * @internal
 */
#[CoversNothing]
final class NoPatchFilesFilterTest extends NormalizatorTestCase
{
    public function testFilter(): void
    {
        $structure = [
            'testing-project' => [
                'patches' => [
                    'patch_1.patch' => '',
                    'patch_2.PATCH' => '',
                    'patch_3.PaTcH' => '',
                    'patch_4.diff' => '',
                    'patch_5.DIFF' => '',
                    'patch_6.DifF' => '',
                ],
                'src' => [],
                'file.php' => '',
            ],
        ];

        vfsStream::create($structure);

        $valid = [
            'vfs://virtual/testing-project' => true,
            'vfs://virtual/testing-project/patches' => true,
            'vfs://virtual/testing-project/patches/patch_1.patch' => false,
            'vfs://virtual/testing-project/patches/patch_2.PATCH' => false,
            'vfs://virtual/testing-project/patches/patch_3.PaTcH' => false,
            'vfs://virtual/testing-project/patches/patch_4.diff' => false,
            'vfs://virtual/testing-project/patches/patch_5.DIFF' => false,
            'vfs://virtual/testing-project/patches/patch_6.DifF' => false,
            'vfs://virtual/testing-project/src' => true,
            'vfs://virtual/testing-project/file.php' => true,
        ];

        $filter = $this->createFilter('no_patch_files');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/testing-project') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }
}
