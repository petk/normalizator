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
class ExtensionNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('pathProvider')]
    public function testNormalize(string $path, string $valid): void
    {
        $normalization = $this->createNormalization('extension');
        $file = new File('vfs://' . $this->virtualRoot->getChild('initial/extension/' . $path)->path());
        $file = $normalization->normalize($file);

        $this->assertSame($valid, $file->getNewFilename());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function pathProvider(): array
    {
        return [
            ['.DS_Store', '.DS_Store'],
            ['noextension', 'noextension'],
            ['foobar.xml', 'foobar.xml'],
            ['fooBar.JPG', 'fooBar.jpg'],
            ['~fooBar.JPEG', '~fooBar.jpg'],
            ['foobar.jpeg', 'foobar.jpg'],
            ['foobar.jpeg-', 'foobar.jpg'],
            ['this.is.some-script', 'this.is.some-script'],
            ['extension-has-space.txt ', 'extension-has-space.txt'],
        ];
    }

    private function testNormalizeWithDuplicatesAfterRename(): void
    {
        // Due to setUp method, we'll loop over files so that we don't loose
        // renamed files between loops.
        /**
         * @var array<string,string>
         */
        $array = [
            'foo bar.txt' => 'foo bar.txt',
            'foo bar.TXT' => 'foo bar.txt',
            'foo--bar.TXT' => 'foo--bar.txt',
            'foo-bar_1.txt' => 'foo-bar_1.txt',
            'foo-bar.txt' => 'foo-bar.txt',
            'foo-bar.TXt' => 'foo-bar.txt',
            'foo-bar.TXT' => 'foo-bar.txt',
        ];

        foreach ($array as $path => $valid) {
            $normalization = $this->createNormalization('extension');
            $file = new File('vfs://' . $this->virtualRoot->getChild('initial/extension-duplicates-after-rename/' . $path)->path());
            $file = $normalization->normalize($file);

            $this->assertSame($valid, $file->getNewFilename());
        }
    }
}
