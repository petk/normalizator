<?php

declare(strict_types=1);

namespace Normalizator\Tests;

use Normalizator\FilterFactory;
use Normalizator\Finder\Finder;
use Normalizator\Normalization\EncodingNormalization;
use Normalizator\Normalization\EolNormalization;
use Normalizator\Normalization\FileExtensionNormalization;
use Normalizator\Normalization\FinalEolNormalization;
use Normalizator\Normalization\LeadingEolNormalization;
use Normalizator\Normalization\MiddleEolNormalization;
use Normalizator\Normalization\PathNameNormalization;
use Normalizator\Normalization\PermissionsNormalization;
use Normalizator\Normalization\SpaceBeforeTabNormalization;
use Normalizator\Normalization\TrailingWhitespaceNormalization;
use Normalizator\NormalizationFactory;
use Normalizator\Observer\NormalizationObserver;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Slugify;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @coversNothing
 */
class NormalizationFactoryTest extends NormalizatorTestCase
{
    /**
     * @param class-string<object> $valid
     */
    #[DataProvider('dataProvider')]
    public function testMake(string $key, string $valid): void
    {
        $finder = new Finder();
        $gitDiscovery = new GitDiscovery();
        $slugify = new Slugify();
        $eolDiscovery = new EolDiscovery($gitDiscovery);
        $filterFactory = new FilterFactory($finder, $gitDiscovery);
        $normalizationObserver = new NormalizationObserver();
        $filenameResolver = new FilenameResolver();

        $factory = new NormalizationFactory(
            $finder,
            $slugify,
            $eolDiscovery,
            $gitDiscovery,
            $filenameResolver,
            $filterFactory,
            $normalizationObserver,
        );

        $this->assertInstanceOf($valid, $factory->make($key));
    }

    /**
     * @return array<int,array<int,class-string|string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['encoding', EncodingNormalization::class],
            ['eol', EolNormalization::class],
            ['extension', FileExtensionNormalization::class],
            ['final-eol', FinalEolNormalization::class],
            ['leading-eol', LeadingEolNormalization::class],
            ['middle-eol', MiddleEolNormalization::class],
            ['path-name', PathNameNormalization::class],
            ['permissions', PermissionsNormalization::class],
            ['space-before-tab', SpaceBeforeTabNormalization::class],
            ['trailing-whitespace', TrailingWhitespaceNormalization::class],
        ];
    }
}
