<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Util;

use Normalizator\Tests\NormalizatorTestCase;
use Normalizator\Util\Glob;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversNothing]
final class GlobTest extends NormalizatorTestCase
{
    #[DataProvider('dataProviderForIsGlob')]
    public function testIsGlob(string $string, bool $isGlob): void
    {
        $glob = new Glob();

        $this->assertSame(
            $isGlob,
            $glob->isGlob($string),
        );
    }

    /**
     * @return array<int,array<int,bool|string>>
     */
    public static function dataProviderForIsGlob(): array
    {
        return [
            ['*.txt', true],
            ['\*.txt', false],
            ['?foo', true],
            ['\?foo', false],
            ['{a,b}', true],
            ['\{a,b\}', false],
            ['[a]', true],
            ['\[a', false],
        ];
    }

    #[DataProvider('dataProviderForConvertToRegex')]
    public function testConvertToRegex(string $globPattern, string $regex): void
    {
        $glob = new Glob();

        $this->assertSame(
            $regex,
            $glob->convertToRegex($globPattern),
        );
    }

    /**
     * @return array<int,array<int,string>>
     */
    public static function dataProviderForConvertToRegex(): array
    {
        return [
            ['*.txt', '/.*\.txt/'],
            ['?xt', '/.xt/'],
            ['??xt', '/..xt/'],
            ['.txt', '/\.txt/'],
            ['*assembly*', '/.*assembly.*/'],
        ];
    }
}
