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
final class LeadingEolNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('leading_eol');
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/leading-eol/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://' . $this->virtualRoot->getChild('fixed/leading-eol/' . $filename)->path(), $file->getPathname());
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
        ];
    }
}
