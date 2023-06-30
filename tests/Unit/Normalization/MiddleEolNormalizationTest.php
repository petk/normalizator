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
final class MiddleEolNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('filesWithMaxOneProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('middle_eol');
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/middle-eol/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://' . $this->virtualRoot->getChild('fixed/middle-eol/' . $filename)->path(), $file->getPathname());
    }

    #[DataProvider('filesWithMaxTwoProvider')]
    public function testNormalizeWithMaxTwo(string $filename): void
    {
        $normalization = $this->createNormalization('middle_eol', ['max_extra_middle_eols' => 2]);
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/middle-eol-2/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://' . $this->virtualRoot->getChild('fixed/middle-eol-2/' . $filename)->path(), $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function filesWithMaxOneProvider(): array
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
            ['file_11.txt'],
            ['file_12.txt'],
        ];
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function filesWithMaxTwoProvider(): array
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
}
