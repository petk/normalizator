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
    public function testNormalize(string $initial, string $fixed): void
    {
        $normalization = $this->createNormalization('encoding');

        $file = new File($this->fixturesRoot . '/' . $initial);

        $valid = file_get_contents($this->fixturesRoot . '/'  . $fixed);

        $this->assertSame($valid, $normalization->normalize($file)->getNewContent());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['initial/encoding/utf-8.txt', 'fixed/encoding/utf-8.txt'],
            // @todo: Not implemented yet.
            // ['initial/encoding/iso-8859-2.txt', 'fixed/encoding/iso-8859-2.txt'],
            ['initial/encoding/windows-1252.txt', 'fixed/encoding/windows-1252.txt'],
        ];
    }
}
