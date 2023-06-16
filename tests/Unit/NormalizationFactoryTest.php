<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit;

use Normalizator\Cache\Cache;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\EventDispatcher\Listener\NormalizationListener;
use Normalizator\EventDispatcher\ListenerProvider;
use Normalizator\FilterFactory;
use Normalizator\Finder\Finder;
use Normalizator\Normalization\EncodingNormalization;
use Normalizator\Normalization\EolNormalization;
use Normalizator\Normalization\ExtensionNormalization;
use Normalizator\Normalization\FinalEolNormalization;
use Normalizator\Normalization\LeadingEolNormalization;
use Normalizator\Normalization\MiddleEolNormalization;
use Normalizator\Normalization\NameNormalization;
use Normalizator\Normalization\PermissionsNormalization;
use Normalizator\Normalization\SpaceBeforeTabNormalization;
use Normalizator\Normalization\TrailingWhitespaceNormalization;
use Normalizator\NormalizationFactory;
use Normalizator\Tests\NormalizatorTestCase;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Logger;
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
        $cache = new Cache();
        $filterFactory = new FilterFactory($finder, $cache, $gitDiscovery);
        $filenameResolver = new FilenameResolver();

        $logger = new Logger();
        $normalizationListener = new NormalizationListener($logger);
        $listenerProvider = new ListenerProvider();
        $listenerProvider->addListener(NormalizationEvent::class, $normalizationListener);
        $eventDispatcher = new EventDispatcher($listenerProvider);

        $factory = new NormalizationFactory(
            $finder,
            $slugify,
            $eolDiscovery,
            $gitDiscovery,
            $filterFactory,
            $eventDispatcher,
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
            ['extension', ExtensionNormalization::class],
            ['final-eol', FinalEolNormalization::class],
            ['leading-eol', LeadingEolNormalization::class],
            ['middle-eol', MiddleEolNormalization::class],
            ['name', NameNormalization::class],
            ['permissions', PermissionsNormalization::class],
            ['space-before-tab', SpaceBeforeTabNormalization::class],
            ['trailing-whitespace', TrailingWhitespaceNormalization::class],
        ];
    }
}
