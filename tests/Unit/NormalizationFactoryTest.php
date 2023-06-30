<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit;

use Normalizator\Normalization\EncodingNormalization;
use Normalizator\Normalization\EolNormalization;
use Normalizator\Normalization\ExtensionNormalization;
use Normalizator\Normalization\FinalEolNormalization;
use Normalizator\Normalization\IndentationNormalization;
use Normalizator\Normalization\LeadingEolNormalization;
use Normalizator\Normalization\MiddleEolNormalization;
use Normalizator\Normalization\NameNormalization;
use Normalizator\Normalization\PermissionsNormalization;
use Normalizator\Normalization\SpaceBeforeTabNormalization;
use Normalizator\Normalization\TrailingWhitespaceNormalization;
use Normalizator\NormalizationFactory;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @coversNothing
 */
final class NormalizationFactoryTest extends NormalizatorTestCase
{
    /**
     * @param class-string<object> $valid
     */
    #[DataProvider('dataProvider')]
    public function testMake(string $key, string $valid): void
    {
        /** @var NormalizationFactory */
        $factory = $this->container->get(NormalizationFactory::class);

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
            ['final_eol', FinalEolNormalization::class],
            ['indentation', IndentationNormalization::class],
            ['leading_eol', LeadingEolNormalization::class],
            ['middle_eol', MiddleEolNormalization::class],
            ['name', NameNormalization::class],
            ['permissions', PermissionsNormalization::class],
            ['space_before_tab', SpaceBeforeTabNormalization::class],
            ['trailing_whitespace', TrailingWhitespaceNormalization::class],
        ];
    }
}
