<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Normalization;

use Normalizator\Enum\Permissions;
use Normalizator\Finder\File;
use Normalizator\Tests\NormalizatorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Normalizator\chmod;

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
        $file = new File($this->fixturesRoot . '/generated/initial/permissions/' . $initialFile);
        $file = $normalization->normalize($file);

        $this->assertSame($validPermissions, $file->getNewPermissions());
    }

    public function testPhar(): void
    {
        $pharFile = $this->fixturesRoot . '/generated/initial/permissions/executable.phar';
        $phar = new \Phar($pharFile);
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();
        $phar->setStub("#!/usr/bin/env php\n<?php Phar::mapPhar('executable.phar'); require 'phar://executable.phar/executable'; __HALT_COMPILER();");
        $phar->addFromString('src/Foobar.php', '<?php class Foobar{}');
        $phar->stopBuffering();
        $phar->compressFiles(\Phar::GZ);
        unset($phar);
        chmod($pharFile, Permissions::EXECUTABLE->get());

        $normalization = $this->createNormalization('permissions');
        $file = new File($this->fixturesRoot . '/generated/initial/permissions/executable.phar');
        $file = $normalization->normalize($file);

        $this->assertSame(Permissions::EXECUTABLE->get(), $file->getNewPermissions());

        // Remove generated phar from disk and memory for next tests run.
        \Phar::unlinkArchive($pharFile);
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public static function filesProvider(): array
    {
        return [
            ['not-a-script.sh', Permissions::FILE->get()],
            ['php-script', Permissions::EXECUTABLE->get()],
            ['Rakefile', Permissions::FILE->get()],
            ['shell-script', Permissions::EXECUTABLE->get()],
            ['shell-script_2', Permissions::EXECUTABLE->get()],
            ['shell-script_3', Permissions::EXECUTABLE->get()],
        ];
    }
}
