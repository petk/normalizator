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
final class SpaceBeforeTabNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testNormalize(string $filename): void
    {
        $normalization = $this->createNormalization('space_before_tab');
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/' . $filename)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals('vfs://' . $this->virtualRoot->getChild('fixed/' . $filename)->path(), $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['space-before-tab/spaceBeforeTab.php'],
            ['miscellaneous/file_1.patch'],
        ];
    }
}
