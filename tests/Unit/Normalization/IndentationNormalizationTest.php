<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Normalization;

use Normalizator\Finder\File;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @coversNothing
 */
class IndentationNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('indentation');
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/indentation/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/indentation/' . $filename, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['file_1.txt'],
            ['file_2.txt'],
            ['file_3.txt'],
            ['file_4.txt'],
            ['file_5.txt'],
            ['file_6.txt'],
            ['file_7.txt'],
            ['file_8.txt'],
        ];
    }

    #[DataProvider('dataProvider2')]
    public function testNormalize2(string $filename): void
    {
        $normalization = $this->createNormalization('indentation', [
            'indentation_size' => 2,
        ]);
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/indentation-2/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/indentation-2/' . $filename, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider2(): array
    {
        return [
            ['file_1.txt'],
            ['file_2.txt'],
            ['file_3.txt'],
        ];
    }
}
