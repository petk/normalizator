<?php

declare(strict_types=1);

namespace Normalizator\Tests\Normalization;

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
        $normalization = $this->createNormalization('final-eol');
        $file = new File('vfs://' . $this->root->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals(__DIR__ . '/../fixtures/' . $fixedFile, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['initial/final-eol/file-1.txt', 'fixed/final-eol/file-1.txt'],
            ['initial/final-eol/file-2.txt', 'fixed/final-eol/file-2.txt'],
            ['initial/final-eol/file-3.txt', 'fixed/final-eol/file-3.txt'],
            ['initial/final-eol/file-4.txt', 'fixed/final-eol/file-4.txt'],
            ['initial/final-eol/file-5.txt', 'fixed/final-eol/file-5.txt'],
            ['initial/final-eol/file-6.txt', 'fixed/final-eol/file-6.txt'],
            ['initial/final-eol/file-7.txt', 'fixed/final-eol/file-7.txt'],
            ['initial/final-eol/file-8.txt', 'fixed/final-eol/file-8.txt'],
        ];
    }
}
