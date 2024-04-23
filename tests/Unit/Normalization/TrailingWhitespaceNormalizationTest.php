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
final class TrailingWhitespaceNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('trailing_whitespace');
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://virtual/fixed/' . $filename, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['trailing-whitespace/fileWithTrailingWhitespace.php'],
            ['trailing-whitespace/no-break-space.txt'],
            ['trailing-whitespace/mongolian-vowel-separator.txt'],
            ['trailing-whitespace/en-quad.txt'],
            ['trailing-whitespace/em-quad.txt'],
            ['trailing-whitespace/en-space.txt'],
            ['trailing-whitespace/em-space.txt'],
            ['trailing-whitespace/three-per-em-space.txt'],
            ['trailing-whitespace/four-per-em-space.txt'],
            ['trailing-whitespace/six-per-em-space.txt'],
            ['trailing-whitespace/figure-space.txt'],
            ['trailing-whitespace/punctuation-space.txt'],
            ['trailing-whitespace/thin-space.txt'],
            ['trailing-whitespace/hair-space.txt'],
            ['trailing-whitespace/narrow-no-break-space.txt'],
            ['trailing-whitespace/medium-mathematical-space.txt'],
            ['trailing-whitespace/ideographic-space.txt'],
            ['trailing-whitespace/zero-width-space.txt'],
            ['trailing-whitespace/zero-width-no-break-space.txt'],
            ['trailing-whitespace/various.txt'],
            ['trailing-whitespace/various-crlf.txt'],
            ['trailing-whitespace/various-cr.txt'],
            ['trailing-whitespace/various-mixed-eol.txt'],
            ['miscellaneous/file_1.patch'],
        ];
    }
}
