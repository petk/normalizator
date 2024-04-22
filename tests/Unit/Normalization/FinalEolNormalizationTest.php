<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Normalization;

use Normalizator\Finder\File;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversNothing]
final class FinalEolNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('final_eol');
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/final-eol/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/final-eol/' . $filename, $file->getPathname());
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
            ['file_9.txt'],
        ];
    }

    #[DataProvider('dataProvider2')]
    public function testNormalizeWithMax2(string $filename): void
    {
        $normalization = $this->createNormalization('final_eol', ['max_extra_final_eols' => 2]);
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/final-eol-2/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/final-eol-2/' . $filename, $file->getPathname());
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
            ['file_4.txt'],
            ['file_5.txt'],
            ['file_6.txt'],
            ['file_7.txt'],
            ['file_8.txt'],
            ['file_9.txt'],
            ['file_10.txt'],
        ];
    }

    #[DataProvider('dataProviderCrlf')]
    public function testNormalizeWithCrlf(string $filename): void
    {
        $normalization = $this->createNormalization('final_eol', [
            'eol' => 'crlf',
            'max_extra_final_eols' => 2,
        ]);
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/final-eol-crlf/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/final-eol-crlf/' . $filename, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProviderCrlf(): array
    {
        return [
            ['file_1.txt'],
            ['file_2.txt'],
        ];
    }
}
