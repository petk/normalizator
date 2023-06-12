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
class PathNameNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('filesProvider')]
    public function testNormalize(string $initialFile, string $fixedFile): void
    {
        $normalization = $this->createNormalization('path-name');
        $file = new File('vfs://' . $this->root->getChild('initial/path-name/' . $initialFile)->path());
        $file = $normalization->normalize($file);
        $file->save();

        $this->assertSame($fixedFile, $file->getNewFilename());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function filesProvider(): array
    {
        return [
            ['update.sample', 'update.sample'],
            ['update.something.sample', 'update.something.sample'],
            ['noextension', 'noextension'],
            ['-foobar.txt', 'foobar.txt'],
            ['~fooBar.xls', '~fooBar.xls'],
            ['äÄëËïÏöÖüÜ.txt', 'aAeEiIoOuU.txt'],
            ['čČćĆšŠđĐžŽ.doc', 'cCcCsSdzDZzZ.doc'],
            ['foo       -    bar.txt', 'foo-bar.txt'],
            ['foo  -     -    bar.txt', 'foo-bar.txt'],
            ['foo - bar.txt', 'foo-bar.txt'],
            ['foo -bar.txt', 'foo-bar.txt'],
            ['foo bar.xml', 'foo-bar.xml'],
            ['foo- bar.txt', 'foo-bar.txt'],
            ['foo--bar.txt', 'foo-bar.txt'],
            ['foo,bar.txt', 'foo-bar.txt'],
            ['foo;Bar.doc', 'foo-Bar.doc'],
            ['foo#bar.txt', 'foo-bar.txt'],
            ['foobar.txt-', 'foobar.txt-'],
            ['fooBar.xls', 'fooBar.xls'],
            ['lorem ipsum & dolor sit amet.txt', 'lorem-ipsum&dolor-sit-amet.txt'],
            ['lorem ipsum   &   dolor sit amet.txt', 'lorem-ipsum&dolor-sit-amet.txt'],
        ];
    }

    public function testNormalizeWithDuplicatesAfterRename(): void
    {
        // Due to setUp method, we'll loop over files so that we don't loose
        // renamed files between loops.
        /**
         * @var array<string,string>
         */
        $array = [
            'foo bar.txt' => 'foo-bar_2.txt',
            'foo--bar.txt' => 'foo-bar_3.txt',
            'foo-bar.txt' => 'foo-bar.txt',
            'foo-bar_1.txt' => 'foo-bar_1.txt',
        ];

        foreach ($array as $initialFile => $fixedFile) {
            $normalization = $this->createNormalization('path-name');
            $file = new File('vfs://' . $this->root->getChild('initial/path-name/files-with-duplicates-after-rename/' . $initialFile)->path());
            $file = $normalization->normalize($file);
            $file->save();

            $this->assertSame($fixedFile, $file->getNewFilename());
        }
    }
}
