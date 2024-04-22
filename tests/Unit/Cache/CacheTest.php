<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Cache;

use Normalizator\Cache\Cache;
use Normalizator\Exception\CacheInvalidArgumentException;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversNothing]
final class CacheTest extends NormalizatorTestCase
{
    #[DataProvider('dataProvider')]
    public function testSet(string $key, bool|int|string $value): void
    {
        /** @var Cache */
        $cache = $this->container->get(Cache::class);

        $cache->set($key, $value);

        $this->assertSame($value, $cache->get($key));
    }

    /**
     * @return array<int,array<int,bool|int|string>>
     */
    public static function dataProvider(): array
    {
        return [
            ['key_1', 'Lorem ipsum'],
            ['key_2', 'Dolor sit amet'],
            ['key_3', 'consectetur adipiscing elit'],
            ['key_4', 42],
            ['key_5', true],
        ];
    }

    public function testSetWithEmptyKey(): void
    {
        /** @var Cache */
        $cache = $this->container->get(Cache::class);

        $this->expectException(CacheInvalidArgumentException::class);

        $cache->set('', 'Lorem ipsum');
    }
}
