<?php

declare(strict_types=1);

namespace Tulia\Bundle\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Tulia\Component\Theme\Customizer\Changeset\Changeset;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration as SymfonyConfiguration;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration extends SymfonyConfiguration
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = parent::getConfigTreeBuilder();
        $root = $treeBuilder->getRootNode();

        $this->registerAssetsConfiguration($root);
        $this->registerAssetterConfiguration($root);
        $this->registerTwigConfiguration($root);
        $this->registerTemplatingConfiguration($root);
        $this->registerThemeConfiguration($root);

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
                                        ->arrayNode('templates')
                                            ->useAttributeAsKey('name')
                                            ->arrayPrototype()
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('template')->isRequired()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('filesystem')
                                    ->children()
                                        ->arrayNode('paths')
                                            ->useAttributeAsKey('name')
                                            ->arrayPrototype()
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('path')->isRequired()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('layout')
                            ->children()
                                ->arrayNode('themes')
                                    ->scalarPrototype()
                                ->end()
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

    private function registerAssetsConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('public_paths')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->defaultValue([])->end()
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
                                    ->arrayNode('included')
                                        ->scalarPrototype()->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerThemeConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('theme')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('changeset')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('base_class')->defaultValue(Changeset::class)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
