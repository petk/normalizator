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
class FinalEolNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('final_eol');
        $file = new File('vfs://' . $this->virtualRoot->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/' . $fixedFile, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['initial/final-eol/file_1.txt', 'fixed/final-eol/file_1.txt'],
            ['initial/final-eol/file_2.txt', 'fixed/final-eol/file_2.txt'],
            ['initial/final-eol/file_3.txt', 'fixed/final-eol/file_3.txt'],
            ['initial/final-eol/file_4.txt', 'fixed/final-eol/file_4.txt'],
            ['initial/final-eol/file_5.txt', 'fixed/final-eol/file_5.txt'],
            ['initial/final-eol/file_6.txt', 'fixed/final-eol/file_6.txt'],
            ['initial/final-eol/file_7.txt', 'fixed/final-eol/file_7.txt'],
            ['initial/final-eol/file_8.txt', 'fixed/final-eol/file_8.txt'],
            ['initial/final-eol/file_9.txt', 'fixed/final-eol/file_9.txt'],
        ];
    }

    #[DataProvider('dataProvider2')]
    public function testNormalizeWithMax2(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('final_eol', ['max_extra_final_eols' => 2]);
        $file = new File('vfs://' . $this->virtualRoot->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/' . $fixedFile, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider2(): array
    {
        return [
            ['initial/final-eol-2/file_1.txt', 'fixed/final-eol-2/file_1.txt'],
            ['initial/final-eol-2/file_2.txt', 'fixed/final-eol-2/file_2.txt'],
            ['initial/final-eol-2/file_3.txt', 'fixed/final-eol-2/file_3.txt'],
            ['initial/final-eol-2/file_4.txt', 'fixed/final-eol-2/file_4.txt'],
            ['initial/final-eol-2/file_5.txt', 'fixed/final-eol-2/file_5.txt'],
            ['initial/final-eol-2/file_6.txt', 'fixed/final-eol-2/file_6.txt'],
            ['initial/final-eol-2/file_7.txt', 'fixed/final-eol-2/file_7.txt'],
            ['initial/final-eol-2/file_8.txt', 'fixed/final-eol-2/file_8.txt'],
            ['initial/final-eol-2/file_9.txt', 'fixed/final-eol-2/file_9.txt'],
            ['initial/final-eol-2/file_10.txt', 'fixed/final-eol-2/file_10.txt'],
        ];
    }

    #[DataProvider('dataProviderCrlf')]
    public function testNormalizeWithCrlf(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('final_eol', [
            'eol' => 'crlf',
            'max_extra_final_eols' => 2,
        ]);
        $file = new File('vfs://' . $this->virtualRoot->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/' . $fixedFile, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProviderCrlf(): array
    {
        return [
            ['initial/final-eol-crlf/file_1.txt', 'fixed/final-eol-crlf/file_1.txt'],
            ['initial/final-eol-crlf/file_2.txt', 'fixed/final-eol-crlf/file_2.txt'],
        ];
    }
}
