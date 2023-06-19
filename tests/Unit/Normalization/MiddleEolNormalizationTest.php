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
        $normalization = $this->createNormalization('middle-eol');
        $file = new File('vfs://' . $this->root->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals($this->fixturesRoot . '/' . $fixedFile, $file->getPathname());
    }

    #[DataProvider('filesWithMaxTwoProvider')]
    public function testNormalizeWithMaxTwo(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('middle-eol', ['max_extra_middle_eols' => 2]);
        $file = new File('vfs://' . $this->root->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals($this->fixturesRoot . '/' . $fixedFile, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function filesWithMaxOneProvider(): array
    {
        return [
            ['initial/middle-eol/file-1.txt', 'fixed/middle-eol/file-1.txt'],
            ['initial/middle-eol/file-2.txt', 'fixed/middle-eol/file-2.txt'],
            ['initial/middle-eol/file-3.txt', 'fixed/middle-eol/file-3.txt'],
            ['initial/middle-eol/file-4.txt', 'fixed/middle-eol/file-4.txt'],
            ['initial/middle-eol/file-5.txt', 'fixed/middle-eol/file-5.txt'],
            ['initial/middle-eol/file-6.txt', 'fixed/middle-eol/file-6.txt'],
            ['initial/middle-eol/file-7.txt', 'fixed/middle-eol/file-7.txt'],
            ['initial/middle-eol/file-8.txt', 'fixed/middle-eol/file-8.txt'],
            ['initial/middle-eol/file-9.txt', 'fixed/middle-eol/file-9.txt'],
            ['initial/middle-eol/file-10.txt', 'fixed/middle-eol/file-10.txt'],
        ];
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function filesWithMaxTwoProvider(): array
    {
        return [
            ['initial/middle-eol-2/file-1.txt', 'fixed/middle-eol-2/file-1.txt'],
            ['initial/middle-eol-2/file-2.txt', 'fixed/middle-eol-2/file-2.txt'],
            ['initial/middle-eol-2/file-3.txt', 'fixed/middle-eol-2/file-3.txt'],
            ['initial/middle-eol-2/file-4.txt', 'fixed/middle-eol-2/file-4.txt'],
            ['initial/middle-eol-2/file-5.txt', 'fixed/middle-eol-2/file-5.txt'],
            ['initial/middle-eol-2/file-6.txt', 'fixed/middle-eol-2/file-6.txt'],
            ['initial/middle-eol-2/file-7.txt', 'fixed/middle-eol-2/file-7.txt'],
            ['initial/middle-eol-2/file-8.txt', 'fixed/middle-eol-2/file-8.txt'],
            ['initial/middle-eol-2/file-9.txt', 'fixed/middle-eol-2/file-9.txt'],
        ];
    }
}
