<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration;

use Normalizator\ConfigurationResolver;
use Normalizator\Finder\File;
use Normalizator\Tests\NormalizatorTestCase;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\GitDiscovery;

/**
 * @internal
 *
 * @coversNothing
 */
class NameNormalizationTest extends NormalizatorTestCase
{
    /**
     * Test extension and name normalization together.
     */
    public function testNormalize(): void
    {
        $gitDiscovery = new GitDiscovery();
        $eolDiscovery = new EolDiscovery($gitDiscovery);
        $configurationResolver = new ConfigurationResolver($eolDiscovery);

        $options = $configurationResolver->resolve([
            'extension' => true,
            'name' => true,
        ]);

        $normalizator = $this->createNormalizator();
        $normalizator->setOptions($options);

        // Due to setUp method, we'll loop over files so that we don't loose
        // renamed files between loops.
        /**
         * @var array<string,string>
         */
        $array = [
            'foo bar.txt' => 'foo-bar_2.txt',
            'foo bar.TXT' => 'foo-bar_3.txt',
            'foo--bar.TXT' => 'foo-bar_4.txt',
            'foo-bar_1.txt' => 'foo-bar_1.txt',
            'foo-bar.txt' => 'foo-bar.txt',
            'foo-bar.TXt' => 'foo-bar_5.txt',
            'foo-bar.TXT' => 'foo-bar_6.txt',
        ];

        foreach ($array as $path => $valid) {
            $file = new File('vfs://' . $this->root->getChild('initial/extensions/files-with-duplicates-after-rename/' . $path)->path());
            $normalizator->normalize($file);
            $normalizator->save($file);

            $this->assertSame($valid, $file->getNewFilename());
            $this->assertFileExists('vfs://tests/initial/extensions/files-with-duplicates-after-rename/' . $valid);
        }
    }
}
