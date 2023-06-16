<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit;

use Normalizator\Cache\Cache;
use Normalizator\Filter\ExecutableFilter;
use Normalizator\Filter\FileFilter;
use Normalizator\Filter\NoGitFilter;
use Normalizator\Filter\NoLinksFilter;
use Normalizator\Filter\NoNodeModulesFilter;
use Normalizator\Filter\NoSvnFilter;
use Normalizator\Filter\NoVendorFilter;
use Normalizator\Filter\PlainTextFilter;
use Normalizator\FilterFactory;
use Normalizator\Finder\Finder;
use Normalizator\Tests\NormalizatorTestCase;
use Normalizator\Util\GitDiscovery;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @coversNothing
 */
class FilterFactoryTest extends NormalizatorTestCase
{
    /**
     * @param class-string<object> $valid
     */
    #[DataProvider('dataProvider')]
    public function testMake(string $key, string $valid): void
    {
        $finder = new Finder();
        $gitDiscover = new GitDiscovery();
        $cache = new Cache();

        $factory = new FilterFactory(
            $finder,
            $cache,
            $gitDiscover,
        );

        $this->assertInstanceOf($valid, $factory->make($key));
    }

    /**
     * @return array<int,array<int,class-string|string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['executable', ExecutableFilter::class],
            ['file', FileFilter::class],
            ['no-git', NoGitFilter::class],
            ['no-links', NoLinksFilter::class],
            ['no-node-modules', NoNodeModulesFilter::class],
            ['no-svn', NoSvnFilter::class],
            ['no-vendor', NoVendorFilter::class],
            ['plain-text', PlainTextFilter::class],
        ];
    }
}
