<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit;

use Normalizator\Filter\FileFilter;
use Normalizator\Filter\NoGitFilter;
use Normalizator\Filter\NoLinksFilter;
use Normalizator\Filter\NoNodeModulesFilter;
use Normalizator\Filter\NoSvnFilter;
use Normalizator\Filter\NoVendorFilter;
use Normalizator\Filter\PlainTextFilter;
use Normalizator\FilterFactory;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversNothing]
final class FilterFactoryTest extends NormalizatorTestCase
{
    /**
     * @param class-string<object> $valid
     */
    #[DataProvider('dataProvider')]
    public function testMake(string $key, string $valid): void
    {
        /** @var FilterFactory */
        $factory = $this->container->get(FilterFactory::class);

        $this->assertInstanceOf($valid, $factory->make($key));
    }

    /**
     * @return array<int,array<int,class-string|string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['file', FileFilter::class],
            ['no_git', NoGitFilter::class],
            ['no_links', NoLinksFilter::class],
            ['no_node_modules', NoNodeModulesFilter::class],
            ['no_svn', NoSvnFilter::class],
            ['no_vendor', NoVendorFilter::class],
            ['plain_text', PlainTextFilter::class],
        ];
    }
}
