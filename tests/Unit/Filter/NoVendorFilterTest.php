<?php

declare(strict_types=1);

namespace Normalizator\Tests\Unit\Filter;

use Normalizator\Finder\Finder;
use Normalizator\Tests\NormalizatorTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * @internal
 *
 * @coversNothing
 */
class NoVendorFilterTest extends NormalizatorTestCase
{
    public function testFilter(): void
    {
        $structure = [
            'php-project' => [
                'vendor' => [
                    'autoload.php' => '<?php',
                    'composer' => [],
                    'dependency' => [
                        'index.php' => '<?php',
                        'src' => [
                            'Application.php' => '<?php',
                        ],
                    ],
                    'other-dependency' => [
                        'vendor' => [
                            'autoload.php' => '<?php',
                            'dependency' => [
                                'app.php' => '',
                            ],
                        ],
                        'index.php' => '<?php',
                        'src' => [
                            'Application.php' => '<?php',
                        ],
                    ],
                ],
                'src' => [],
                'index.php' => '<?php',
            ],
        ];

        /**
         * @var array<string,bool>
         */
        $valid = [
            'vfs://virtual/php-project' => true,
            'vfs://virtual/php-project/vendor' => false,
            'vfs://virtual/php-project/vendor/autoload.php' => false,
            'vfs://virtual/php-project/vendor/composer' => false,
            'vfs://virtual/php-project/vendor/dependency' => false,
            'vfs://virtual/php-project/vendor/dependency/index.php' => false,
            'vfs://virtual/php-project/vendor/dependency/src' => false,
            'vfs://virtual/php-project/vendor/dependency/src/Application.php' => false,
            'vfs://virtual/php-project/vendor/other-dependency' => false,
            'vfs://virtual/php-project/vendor/other-dependency/vendor' => false,
            'vfs://virtual/php-project/vendor/other-dependency/vendor/autoload.php' => false,
            'vfs://virtual/php-project/vendor/other-dependency/vendor/dependency' => false,
            'vfs://virtual/php-project/vendor/other-dependency/vendor/dependency/app.php' => false,
            'vfs://virtual/php-project/vendor/other-dependency/index.php' => false,
            'vfs://virtual/php-project/vendor/other-dependency/src' => false,
            'vfs://virtual/php-project/vendor/other-dependency/src/Application.php' => false,
            'vfs://virtual/php-project/src' => true,
            'vfs://virtual/php-project/index.php' => true,
        ];

        vfsStream::create($structure);

        $filter = $this->createFilter('no-vendor');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/php-project') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }

    public function testFilter2(): void
    {
        $structure = [
            'non-php-project' => [
                'vendor' => [
                    'dependency' => [
                        'index.php' => '<?php',
                        'src' => [
                            'Application.php' => '<?php',
                        ],
                    ],
                ],
                'src' => [],
                'index.php' => '<?php',
            ],
        ];

        $valid = [
            'vfs://virtual/non-php-project' => true,
            'vfs://virtual/non-php-project/vendor' => true,
            'vfs://virtual/non-php-project/vendor/dependency' => true,
            'vfs://virtual/non-php-project/vendor/dependency/index.php' => true,
            'vfs://virtual/non-php-project/vendor/dependency/src' => true,
            'vfs://virtual/non-php-project/vendor/dependency/src/Application.php' => true,
            'vfs://virtual/non-php-project/src' => true,
            'vfs://virtual/non-php-project/index.php' => true,
        ];

        vfsStream::create($structure);

        $filter = $this->createFilter('no-vendor');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/non-php-project') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }

    public function testFilter3(): void
    {
        $structure = [
            'non-php-project' => [
                'asdfvendor' => [
                    'autoload.php' => '<?php',
                    'composer' => [],
                    'dependency' => [
                        'index.php' => '<?php',
                        'src' => [
                            'Application.php' => '<?php',
                        ],
                    ],
                ],
                'src' => [],
                'index.php' => '<?php',
            ],
        ];

        $valid = [
            'vfs://virtual/non-php-project' => true,
            'vfs://virtual/non-php-project/asdfvendor' => true,
            'vfs://virtual/non-php-project/asdfvendor/autoload.php' => true,
            'vfs://virtual/non-php-project/asdfvendor/composer' => true,
            'vfs://virtual/non-php-project/asdfvendor/dependency' => true,
            'vfs://virtual/non-php-project/asdfvendor/dependency/index.php' => true,
            'vfs://virtual/non-php-project/asdfvendor/dependency/src' => true,
            'vfs://virtual/non-php-project/asdfvendor/dependency/src/Application.php' => true,
            'vfs://virtual/non-php-project/src' => true,
            'vfs://virtual/non-php-project/index.php' => true,
        ];

        vfsStream::create($structure);

        $filter = $this->createFilter('no-vendor');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/non-php-project') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }

    public function testFilter4(): void
    {
        $structure = [
            'non-php-project' => [
                'vendorasdf' => [
                    'autoload.php' => '<?php',
                    'composer' => [],
                    'dependency' => [
                        'index.php' => '<?php',
                        'src' => [
                            'Application.php' => '<?php',
                        ],
                    ],
                ],
                'src' => [],
                'index.php' => '<?php',
            ],
        ];

        $valid = [
            'vfs://virtual/non-php-project' => true,
            'vfs://virtual/non-php-project/vendorasdf' => true,
            'vfs://virtual/non-php-project/vendorasdf/autoload.php' => true,
            'vfs://virtual/non-php-project/vendorasdf/composer' => true,
            'vfs://virtual/non-php-project/vendorasdf/dependency' => true,
            'vfs://virtual/non-php-project/vendorasdf/dependency/index.php' => true,
            'vfs://virtual/non-php-project/vendorasdf/dependency/src' => true,
            'vfs://virtual/non-php-project/vendorasdf/dependency/src/Application.php' => true,
            'vfs://virtual/non-php-project/src' => true,
            'vfs://virtual/non-php-project/index.php' => true,
        ];

        vfsStream::create($structure);

        $filter = $this->createFilter('no-vendor');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/non-php-project') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }
}
