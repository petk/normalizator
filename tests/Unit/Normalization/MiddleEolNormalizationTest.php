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
class MiddleEolNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('filesWithMaxOneProvider')]
    public function testNormalize(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('middle_eol');
        $file = new File('vfs://' . $this->virtualRoot->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://' . $this->virtualRoot->getChild($fixedFile)->path(), $file->getPathname());
    }

    #[DataProvider('filesWithMaxTwoProvider')]
    public function testNormalizeWithMaxTwo(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('middle_eol', ['max_extra_middle_eols' => 2]);
        $file = new File('vfs://' . $this->virtualRoot->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://' . $this->virtualRoot->getChild($fixedFile)->path(), $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function filesWithMaxOneProvider(): array
    {
        return [
            ['initial/middle-eol/file_1.txt', 'fixed/middle-eol/file_1.txt'],
            ['initial/middle-eol/file_2.txt', 'fixed/middle-eol/file_2.txt'],
            ['initial/middle-eol/file_3.txt', 'fixed/middle-eol/file_3.txt'],
            ['initial/middle-eol/file_4.txt', 'fixed/middle-eol/file_4.txt'],
            ['initial/middle-eol/file_5.txt', 'fixed/middle-eol/file_5.txt'],
            ['initial/middle-eol/file_6.txt', 'fixed/middle-eol/file_6.txt'],
            ['initial/middle-eol/file_7.txt', 'fixed/middle-eol/file_7.txt'],
            ['initial/middle-eol/file_8.txt', 'fixed/middle-eol/file_8.txt'],
            ['initial/middle-eol/file_9.txt', 'fixed/middle-eol/file_9.txt'],
            ['initial/middle-eol/file_10.txt', 'fixed/middle-eol/file_10.txt'],
        ];
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function filesWithMaxTwoProvider(): array
    {
        return [
            ['initial/middle-eol-2/file_1.txt', 'fixed/middle-eol-2/file_1.txt'],
            ['initial/middle-eol-2/file_2.txt', 'fixed/middle-eol-2/file_2.txt'],
            ['initial/middle-eol-2/file_3.txt', 'fixed/middle-eol-2/file_3.txt'],
            ['initial/middle-eol-2/file_4.txt', 'fixed/middle-eol-2/file_4.txt'],
            ['initial/middle-eol-2/file_5.txt', 'fixed/middle-eol-2/file_5.txt'],
            ['initial/middle-eol-2/file_6.txt', 'fixed/middle-eol-2/file_6.txt'],
            ['initial/middle-eol-2/file_7.txt', 'fixed/middle-eol-2/file_7.txt'],
            ['initial/middle-eol-2/file_8.txt', 'fixed/middle-eol-2/file_8.txt'],
            ['initial/middle-eol-2/file_9.txt', 'fixed/middle-eol-2/file_9.txt'],
        ];
    }
}
