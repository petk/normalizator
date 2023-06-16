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
class NoNodeModulesFilterTest extends NormalizatorTestCase
{
    public function testFilter(): void
    {
        $structure = [
            'node-project' => [
                'node_modules' => [
                    '.package-lock.json' => '',
                    'dependency' => [
                        'index.js' => '',
                        'src' => [
                            'app.js' => '',
                        ],
                    ],
                    'other-dependency' => [
                        'index.js' => '',
                        'node_modules' => [
                            'child-dependency' => [
                                'index.js' => '',
                            ],
                        ],
                        'src' => [
                            'app.js' => '',
                        ],
                    ],
                ],
                'src' => [],
                'app.js' => '',
            ],
        ];

        vfsStream::create($structure);

        $valid = [
            'vfs://tests/node-project' => true,
            'vfs://tests/node-project/node_modules' => false,
            'vfs://tests/node-project/node_modules/.package-lock.json' => false,
            'vfs://tests/node-project/node_modules/dependency' => false,
            'vfs://tests/node-project/node_modules/dependency/index.js' => false,
            'vfs://tests/node-project/node_modules/dependency/src' => false,
            'vfs://tests/node-project/node_modules/dependency/src/app.js' => false,
            'vfs://tests/node-project/node_modules/other-dependency' => false,
            'vfs://tests/node-project/node_modules/other-dependency/index.js' => false,
            'vfs://tests/node-project/node_modules/other-dependency/node_modules' => false,
            'vfs://tests/node-project/node_modules/other-dependency/node_modules/child-dependency' => false,
            'vfs://tests/node-project/node_modules/other-dependency/node_modules/child-dependency/index.js' => false,
            'vfs://tests/node-project/node_modules/other-dependency/src' => false,
            'vfs://tests/node-project/node_modules/other-dependency/src/app.js' => false,
            'vfs://tests/node-project/src' => true,
            'vfs://tests/node-project/app.js' => true,
        ];

        $filter = $this->createFilter('no-node-modules');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://tests/node-project') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }

    public function testFilter2(): void
    {
        $structure = [
            'non-node-project' => [
                'node_modules' => [
                    'dependency' => [
                        'index.js' => '',
                        'src' => [
                            'app.js' => '',
                        ],
                    ],
                ],
                'src' => [],
                'index.js' => '',
            ],
        ];

        vfsStream::create($structure);

        $valid = [
            'vfs://tests/non-node-project' => true,
            'vfs://tests/non-node-project/node_modules' => true,
            'vfs://tests/non-node-project/node_modules/dependency' => true,
            'vfs://tests/non-node-project/node_modules/dependency/index.js' => true,
            'vfs://tests/non-node-project/node_modules/dependency/src' => true,
            'vfs://tests/non-node-project/node_modules/dependency/src/app.js' => true,
            'vfs://tests/non-node-project/src' => true,
            'vfs://tests/non-node-project/index.js' => true,
        ];

        $filter = $this->createFilter('no-node-modules');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://tests/non-node-project/') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }

    public function testFilter3(): void
    {
        $structure = [
            'non-node-project' => [
                'asdfnode_modules' => [
                    '.package-lock.json' => '',
                    'dependency' => [
                        'index.js' => '',
                        'src' => [
                            'app.js' => '',
                        ],
                    ],
                ],
                'src' => [],
                'index.js' => '',
            ],
        ];

        vfsStream::create($structure);

        $valid = [
            'vfs://tests/non-node-project' => true,
            'vfs://tests/non-node-project/asdfnode_modules' => true,
            'vfs://tests/non-node-project/asdfnode_modules/.package-lock.json' => true,
            'vfs://tests/non-node-project/asdfnode_modules/dependency' => true,
            'vfs://tests/non-node-project/asdfnode_modules/dependency/index.js' => true,
            'vfs://tests/non-node-project/asdfnode_modules/dependency/src' => true,
            'vfs://tests/non-node-project/asdfnode_modules/dependency/src/app.js' => true,
            'vfs://tests/non-node-project/src' => true,
            'vfs://tests/non-node-project/index.js' => true,
        ];

        $filter = $this->createFilter('no-node-modules');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://tests/non-node-project/') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }

    public function testFilter4(): void
    {
        $structure = [
            'non-node-project' => [
                'node_modulesasdf' => [
                    '.package-lock.json' => '',
                    'dependency' => [
                        'index.js' => '',
                        'src' => [
                            'app.js' => '',
                        ],
                    ],
                ],
                'src' => [],
                'index.js' => '',
            ],
        ];

        vfsStream::create($structure);

        $valid = [
            'vfs://tests/non-node-project' => true,
            'vfs://tests/non-node-project/node_modulesasdf' => true,
            'vfs://tests/non-node-project/node_modulesasdf/.package-lock.json' => true,
            'vfs://tests/non-node-project/node_modulesasdf/dependency' => true,
            'vfs://tests/non-node-project/node_modulesasdf/dependency/index.js' => true,
            'vfs://tests/non-node-project/node_modulesasdf/dependency/src' => true,
            'vfs://tests/non-node-project/node_modulesasdf/dependency/src/app.js' => true,
            'vfs://tests/non-node-project/src' => true,
            'vfs://tests/non-node-project/index.js' => true,
        ];

        $filter = $this->createFilter('no-node-modules');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://tests/non-node-project/') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }
}
