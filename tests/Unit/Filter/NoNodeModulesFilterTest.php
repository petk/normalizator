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
final class NoNodeModulesFilterTest extends NormalizatorTestCase
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
            'vfs://virtual/node-project' => true,
            'vfs://virtual/node-project/node_modules' => false,
            'vfs://virtual/node-project/node_modules/.package-lock.json' => false,
            'vfs://virtual/node-project/node_modules/dependency' => false,
            'vfs://virtual/node-project/node_modules/dependency/index.js' => false,
            'vfs://virtual/node-project/node_modules/dependency/src' => false,
            'vfs://virtual/node-project/node_modules/dependency/src/app.js' => false,
            'vfs://virtual/node-project/node_modules/other-dependency' => false,
            'vfs://virtual/node-project/node_modules/other-dependency/index.js' => false,
            'vfs://virtual/node-project/node_modules/other-dependency/node_modules' => false,
            'vfs://virtual/node-project/node_modules/other-dependency/node_modules/child-dependency' => false,
            'vfs://virtual/node-project/node_modules/other-dependency/node_modules/child-dependency/index.js' => false,
            'vfs://virtual/node-project/node_modules/other-dependency/src' => false,
            'vfs://virtual/node-project/node_modules/other-dependency/src/app.js' => false,
            'vfs://virtual/node-project/src' => true,
            'vfs://virtual/node-project/app.js' => true,
        ];

        $filter = $this->createFilter('no_node_modules');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/node-project') as $key => $file) {
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
            'vfs://virtual/non-node-project' => true,
            'vfs://virtual/non-node-project/node_modules' => true,
            'vfs://virtual/non-node-project/node_modules/dependency' => true,
            'vfs://virtual/non-node-project/node_modules/dependency/index.js' => true,
            'vfs://virtual/non-node-project/node_modules/dependency/src' => true,
            'vfs://virtual/non-node-project/node_modules/dependency/src/app.js' => true,
            'vfs://virtual/non-node-project/src' => true,
            'vfs://virtual/non-node-project/index.js' => true,
        ];

        $filter = $this->createFilter('no_node_modules');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/non-node-project/') as $key => $file) {
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
            'vfs://virtual/non-node-project' => true,
            'vfs://virtual/non-node-project/asdfnode_modules' => true,
            'vfs://virtual/non-node-project/asdfnode_modules/.package-lock.json' => true,
            'vfs://virtual/non-node-project/asdfnode_modules/dependency' => true,
            'vfs://virtual/non-node-project/asdfnode_modules/dependency/index.js' => true,
            'vfs://virtual/non-node-project/asdfnode_modules/dependency/src' => true,
            'vfs://virtual/non-node-project/asdfnode_modules/dependency/src/app.js' => true,
            'vfs://virtual/non-node-project/src' => true,
            'vfs://virtual/non-node-project/index.js' => true,
        ];

        $filter = $this->createFilter('no_node_modules');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/non-node-project/') as $key => $file) {
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
            'vfs://virtual/non-node-project' => true,
            'vfs://virtual/non-node-project/node_modulesasdf' => true,
            'vfs://virtual/non-node-project/node_modulesasdf/.package-lock.json' => true,
            'vfs://virtual/non-node-project/node_modulesasdf/dependency' => true,
            'vfs://virtual/non-node-project/node_modulesasdf/dependency/index.js' => true,
            'vfs://virtual/non-node-project/node_modulesasdf/dependency/src' => true,
            'vfs://virtual/non-node-project/node_modulesasdf/dependency/src/app.js' => true,
            'vfs://virtual/non-node-project/src' => true,
            'vfs://virtual/non-node-project/index.js' => true,
        ];

        $filter = $this->createFilter('no_node_modules');

        $finder = new Finder();

        foreach ($finder->getTree('vfs://virtual/non-node-project/') as $key => $file) {
            $this->assertSame($valid[$key], $filter->filter($file));
        }
    }
}
