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
        $normalization = $this->createNormalization('leading-eol');
        $file = new File('vfs://' . $this->root->getChild($initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertFileEquals($this->fixturesRoot . '/' . $fixedFile, $file->getPathname());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['initial/leading-eol/file-1.txt', 'fixed/leading-eol/file-1.txt'],
            ['initial/leading-eol/file-2.txt', 'fixed/leading-eol/file-2.txt'],
            ['initial/leading-eol/file-3.txt', 'fixed/leading-eol/file-3.txt'],
            ['initial/leading-eol/file-4.txt', 'fixed/leading-eol/file-4.txt'],
            ['initial/leading-eol/file-5.txt', 'fixed/leading-eol/file-5.txt'],
        ];
    }
}
