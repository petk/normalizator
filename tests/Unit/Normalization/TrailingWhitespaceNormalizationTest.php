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
final class TrailingWhitespaceNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('trailing_whitespace');
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/trailing-whitespace/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/trailing-whitespace/' . $filename, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['fileWithTrailingWhitespace.php'],
            ['no-break-space.txt'],
            ['mongolian-vowel-separator.txt'],
            ['en-quad.txt'],
            ['em-quad.txt'],
            ['en-space.txt'],
            ['em-space.txt'],
            ['three-per-em-space.txt'],
            ['four-per-em-space.txt'],
            ['six-per-em-space.txt'],
            ['figure-space.txt'],
            ['punctuation-space.txt'],
            ['thin-space.txt'],
            ['hair-space.txt'],
            ['narrow-no-break-space.txt'],
            ['medium-mathematical-space.txt'],
            ['ideographic-space.txt'],
            ['zero-width-space.txt'],
            ['zero-width-no-break-space.txt'],
            ['various.txt'],
            ['various-crlf.txt'],
            ['various-cr.txt'],
            ['various-mixed-eol.txt'],
        ];
    }
}
