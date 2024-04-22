<?php

declare(strict_types=1);

namespace Normalizator\Tests\Integration;

use Normalizator\Configuration\Configuration;
use Normalizator\Configuration\ConfigurationResolver;
use Normalizator\Finder\File;
use Normalizator\Normalizator;
use Normalizator\NormalizatorInterface;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * @internal
 */
#[CoversNothing]
final class NameNormalizationTest extends NormalizatorTestCase
{
    /**
     * Test extension and name normalization together.
     */
    public function testNormalize(): void
    {
        /** @var ConfigurationResolver */
        $configurationResolver = $this->container->get(ConfigurationResolver::class);

        $options = $configurationResolver->resolve([
            'extension' => true,
            'name' => true,
        ]);

        /** @var Configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->setMultiple($options);

        /** @var NormalizatorInterface */
        $normalizator = $this->container->get(Normalizator::class);

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
            $file = new File('vfs://' . $this->virtualRoot->getChild('initial/extension-duplicates-after-rename/' . $path)->path());
            $normalizator->normalize($file);
            $normalizator->save($file);

            $this->assertSame($valid, $file->getNewFilename());
            $this->assertFileExists('vfs://virtual/initial/extension-duplicates-after-rename/' . $valid);
        }
    }
}
