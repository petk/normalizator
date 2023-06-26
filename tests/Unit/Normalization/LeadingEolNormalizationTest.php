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
class LeadingEolNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('leading_eol');
        $file = new File('vfs://' . $this->virtualRoot->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://' . $this->virtualRoot->getChild($fixedFile)->path(), $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['initial/leading-eol/file_1.txt', 'fixed/leading-eol/file_1.txt'],
            ['initial/leading-eol/file_2.txt', 'fixed/leading-eol/file_2.txt'],
            ['initial/leading-eol/file_3.txt', 'fixed/leading-eol/file_3.txt'],
            ['initial/leading-eol/file_4.txt', 'fixed/leading-eol/file_4.txt'],
            ['initial/leading-eol/file_5.txt', 'fixed/leading-eol/file_5.txt'],
            ['initial/leading-eol/file_6.txt', 'fixed/leading-eol/file_6.txt'],
        ];
    }
}
