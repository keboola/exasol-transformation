<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\Config;

use Keboola\Component\Config\BaseConfigDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ConfigDefinition extends BaseConfigDefinition
{
    protected function getParametersDefinition(): ArrayNodeDefinition
    {
        $parametersNode = parent::getParametersDefinition();
        // @formatter:off
        /** @noinspection NullPointerExceptionInspection */
        $parametersNode
            ->children()
                ->arrayNode('blocks')
                    ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')->end()
                        ->arrayNode('codes')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('name')->end()
                                ->arrayNode('script')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        // @formatter:on
        return $parametersNode;
    }
}
