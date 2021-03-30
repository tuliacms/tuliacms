<?php

declare(strict_types=1);

namespace Tulia\Framework\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('framework');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('routing')
                    ->children()
                        ->scalarNode('directory_list')->defaultValue([])->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
