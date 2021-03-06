<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\Tests;

use Generator;
use Keboola\ExasolTransformation\Config\Config;
use Keboola\ExasolTransformation\Config\ConfigDefinition;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigTest extends TestCase
{
    /**
     * @dataProvider validConfigDataProvider
     */
    public function testValidConfig(array $configArray): void
    {
        $config = new Config($configArray, new ConfigDefinition());

        Assert::assertEquals($configArray, $config->getData());
    }

    /**
     * @dataProvider invalidConfigDataProvider
     */
    public function testInvalidConfig(array $configArray, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        new Config($configArray, new ConfigDefinition());
    }



    public function testQueryTimeoutDefaultValue(): void
    {
        $config = new Config([
            'authorization' => $this->getAuthorizationNode(),
            'parameters' => [
                'blocks' => [
                    [
                        'name' => 'name of the blocks',
                        'codes' => [],
                    ],
                ],
            ],
        ], new ConfigDefinition());

        Assert::assertSame(7200, $config->getQueryTimeout());
    }

    public function testQueryTimeoutFromParams(): void
    {
        $config = new Config([
            'authorization' => $this->getAuthorizationNode(),
            'parameters' => [
                'query_timeout' => 14400,
                'blocks' => [
                    [
                        'name' => 'name of the blocks',
                        'codes' => [],
                    ],
                ],
            ],
        ], new ConfigDefinition());

        Assert::assertSame(14400, $config->getQueryTimeout());
    }

    public function validConfigDataProvider(): Generator
    {
        yield 'minimal-valid-config' => [
            [
                'parameters' => [
                    'query_timeout' => 7200,
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [
                                    'name' => 'name of the code',
                                    'script' => [
                                        'SELECT 1;',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'authorization' => $this->getAuthorizationNode(),
            ],
        ];
    }

    public function invalidConfigDataProvider(): Generator
    {
        yield 'missing-blocks' => [
            [
                'parameters' => [],
            ],
            'The child config "blocks" under "root.parameters" must be configured.',
        ];

        yield 'missing-block-name' => [
            [
                'parameters' => [
                    'blocks' => [
                        [],
                    ],
                ],
            ],
            'The child config "name" under "root.parameters.blocks.0" must be configured.',
        ];

        yield 'missing-block-codes' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                        ],
                    ],
                ],
            ],
            'The child config "codes" under "root.parameters.blocks.0" must be configured.',
        ];

        yield 'missing-block-code-name' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [],
                            ],
                        ],
                    ],
                ],
            ],
            'The child config "name" under "root.parameters.blocks.0.codes.0" must be configured.',
        ];

        yield 'missing-block-code-script' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [
                                    'name' => 'name of the code',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'The child config "script" under "root.parameters.blocks.0.codes.0" must be configured.',
        ];

        yield 'missing-authorization' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [
                                    'name' => 'name of the code',
                                    'script' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'The child config "authorization" under "root" must be configured.',
        ];

        yield 'missing-authorization-workspace' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [
                                    'name' => 'name of the code',
                                    'script' => [],
                                ],
                            ],
                        ],
                    ],
                ],
                'authorization' => [],
            ],
            'The child config "workspace" under "root.authorization" must be configured.',
        ];

        yield 'missing-authorization-host' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [
                                    'name' => 'name of the code',
                                    'script' => [],
                                ],
                            ],
                        ],
                    ],
                ],
                'authorization' => [
                    'workspace' => [
                        'port' => 12345,
                        'user' => 'user',
                        'password' => 'secret password',
                        'schema' => 'db schema',
                    ],
                ],
            ],
            'The child config "host" under "root.authorization.workspace" must be configured.',
        ];

        yield 'missing-authorization-user' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [
                                    'name' => 'name of the code',
                                    'script' => [],
                                ],
                            ],
                        ],
                    ],
                ],
                'authorization' => [
                    'workspace' => [
                        'host' => 'db host',
                        'port' => 12345,
                        'password' => 'secret password',
                        'schema' => 'db schema',
                    ],
                ],
            ],
            'The child config "user" under "root.authorization.workspace" must be configured.',
        ];

        yield 'missing-authorization-password' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [
                                    'name' => 'name of the code',
                                    'script' => [],
                                ],
                            ],
                        ],
                    ],
                ],
                'authorization' => [
                    'workspace' => [
                        'host' => 'db host',
                        'port' => 12345,
                        'user' => 'user',
                        'schema' => 'db schema',
                    ],
                ],
            ],
            'The child config "password" under "root.authorization.workspace" must be configured.',
        ];

        yield 'missing-authorization-schema' => [
            [
                'parameters' => [
                    'blocks' => [
                        [
                            'name' => 'name of the block',
                            'codes' => [
                                [
                                    'name' => 'name of the code',
                                    'script' => [],
                                ],
                            ],
                        ],
                    ],
                ],
                'authorization' => [
                    'workspace' => [
                        'host' => 'db host',
                        'port' => 12345,
                        'user' => 'user',
                        'password' => 'secret password',
                    ],
                ],
            ],
            'The child config "schema" under "root.authorization.workspace" must be configured.',
        ];
    }

    private function getAUthorizationNode(): array
    {
        return [
            'workspace' => [
                'host' => 'host',
                'port' => 12345,
                'user' => 'user',
                'password' => 'secret password',
                'schema' => 'db schema',
            ],
        ];
    }
}
