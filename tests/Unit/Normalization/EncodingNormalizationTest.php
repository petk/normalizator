<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Normalization;

use Normalizator\Finder\File;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Normalizator\file_get_contents;

/**
 * @internal
 *
 * @coversNothing
 */
class EncodingNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('encoding');

        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/encoding/' . $filename)->path());
        $file = $normalization->normalize($file);

        $valid = file_get_contents('vfs://' . $this->virtualRoot->getChild('fixed/encoding/' . $filename)->path());

        $this->assertSame($valid, $file->getNewContent());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['windows-1252.txt'],
            ['utf-8.txt'],
            // @todo: Not implemented yet.
            // ['iso-8859-2.txt'],
        ];
    }
}
