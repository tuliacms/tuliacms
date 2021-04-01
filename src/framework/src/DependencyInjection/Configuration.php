<?php

declare(strict_types=1);

namespace Tulia\Framework\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
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
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->arrayNode('routing')
                    ->children()
                        ->scalarNode('directory_list')->defaultValue([])->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->registerAssetterConfiguration($root);
        $this->registerTwigConfiguration($root);
        $this->registerTemplatingConfiguration($root);

        return $treeBuilder;
    }

    private function registerTwigConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('twig')
                    ->children()
                        ->arrayNode('loader')
                            ->children()
                                ->arrayNode('array')
                                    ->children()
                                        ->scalarNode('templates')->defaultValue([])->end()
                                    ->end()
                                ->end()
                                ->arrayNode('filesystem')
                                    ->children()
                                        ->scalarNode('paths')->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('layout')
                            ->children()
                                ->scalarNode('themes')->defaultValue([])->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerTemplatingConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('templating')
                    ->children()
                        ->arrayNode('paths')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->scalarNode('path')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('namespace_overwrite')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('from')->isRequired()->end()
                                    ->scalarNode('to')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerAssetterConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('assetter')
                    ->fixXmlConfig('asset')
                    ->children()
                        ->arrayNode('assets')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->arrayNode('scripts')
                                        ->scalarPrototype()->defaultValue([])->end()
                                    ->end()
                                    ->arrayNode('styles')
                                        ->scalarPrototype()->defaultValue([])->end()
                                    ->end()
                                    ->arrayNode('require')
                                        ->scalarPrototype()->defaultValue([])->end()
                                    ->end()
                                    ->scalarNode('group')->defaultValue('body')->end()
                                    ->scalarNode('priority')->defaultValue('100')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
