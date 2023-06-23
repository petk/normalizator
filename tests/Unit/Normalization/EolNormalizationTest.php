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
class EolNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('lfDataProvider')]
    public function testNormalize(string $initial, string $fixed): void
    {
        $normalization = $this->createNormalization('eol', ['eol' => 'lf']);

        $file = new File('vfs://tests/' . $initial);

        $valid = file_get_contents('vfs://tests/'  . $fixed);

        $this->assertSame($valid, $normalization->normalize($file)->getNewContent());
    }

    #[DataProvider('crlfDataProvider')]
    public function testNormalizeCrlf(string $initial, string $fixed): void
    {
        $normalization = $this->createNormalization('eol', ['eol' => 'crlf']);

        $file = new File('vfs://tests/' . $initial);

        $valid = file_get_contents('vfs://tests/'  . $fixed);

        $this->assertSame($valid, $normalization->normalize($file)->getNewContent());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function lfDataProvider(): array
    {
        return [
            ['initial/eol/lf/file_1.txt', 'fixed/eol/lf/file_1.txt'],
            ['initial/eol/lf/file_2.txt', 'fixed/eol/lf/file_2.txt'],
            ['initial/eol/lf/file_3.txt', 'fixed/eol/lf/file_3.txt'],
        ];
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function crlfDataProvider(): array
    {
        return [
            ['initial/eol/crlf/file_1.txt', 'fixed/eol/crlf/file_1.txt'],
            ['initial/eol/crlf/file_2.txt', 'fixed/eol/crlf/file_2.txt'],
            ['initial/eol/crlf/file_3.txt', 'fixed/eol/crlf/file_3.txt'],
        ];
    }
}
