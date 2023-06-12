#!/usr/bin/env php
<?php

/**
 * Helper script that creates phar file for test fixtures.
 */

declare(strict_types=1);

use Normalizator\Enum\Permissions;

use function Normalizator\chmod;

require __DIR__ . '/../vendor/autoload.php';

$phar = new \Phar('executable.phar');

$phar->setSignatureAlgorithm(\Phar::SHA1);

$phar->startBuffering();

$phar->setStub("#!/usr/bin/env php\n<?php Phar::mapPhar('executable.phar'); require 'phar://executable.phar/executable'; __HALT_COMPILER();");

$phar->addFromString('src/Foobar.php', '<?php class Foobar{}');

$phar->stopBuffering();

$phar->compressFiles(\Phar::GZ);

unset($phar);
chmod('executable.phar', Permissions::EXECUTABLE->get());
