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
final class EolNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('lfDataProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('eol', ['eol' => 'lf']);
        $file = new File('vfs://virtual/initial/eol/lf/' . $filename);
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/eol/lf/' . $filename, $file->getPathname());
    }

    #[DataProvider('crlfDataProvider')]
    public function testNormalizeCrlf(string $filename): void
    {
        $normalization = $this->createNormalization('eol', ['eol' => 'crlf']);
        $file = new File('vfs://virtual/initial/eol/crlf/' . $filename);
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/eol/crlf/' . $filename, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function lfDataProvider(): array
    {
        return [
            ['file_1.txt'],
            ['file_2.txt'],
            ['file_3.txt'],
        ];
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function crlfDataProvider(): array
    {
        return [
            ['file_1.txt'],
            ['file_2.txt'],
            ['file_3.txt'],
        ];
    }
}
