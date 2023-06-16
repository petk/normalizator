<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Normalization;

use Normalizator\Enum\Permissions;
use Normalizator\Finder\File;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @coversNothing
 */
class PermissionsNormalizationTest extends NormalizatorTestCase
{
    #[DataProvider('filesProvider')]
    public function testNormalize(string $initialFile, int $validPermissions): void
    {
        $normalization = $this->createNormalization('permissions');
        $file = new File($this->fixturesRoot . '/initial/permissions/' . $initialFile);
        $file = $normalization->normalize($file);

        $this->assertSame($validPermissions, $file->getNewPermissions());
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public static function filesProvider(): array
    {
        return [
            ['executable.phar', Permissions::EXECUTABLE->get()],
            ['not-a-script.sh', Permissions::FILE->get()],
            ['php-script', Permissions::EXECUTABLE->get()],
            ['Rakefile', Permissions::FILE->get()],
            ['shell-script', Permissions::EXECUTABLE->get()],
            ['shell-script-2', Permissions::EXECUTABLE->get()],
            ['shell-script-3', Permissions::EXECUTABLE->get()],
        ];
    }
}
