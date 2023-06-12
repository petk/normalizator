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
class TrailingWhitespaceNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('trailing-whitespace');
        $file = new File('vfs://' . $this->root->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals(__DIR__ . '/../fixtures/' . $fixedFile, $file->getPathname());
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['initial/trailing-whitespace/fileWithTrailingSpaces.php', 'fixed/trailing-whitespace/fileWithTrailingSpaces.php'],
        ];
    }
}
