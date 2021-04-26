<?php

declare(strict_types=1);

namespace Tulia\Framework\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;
use Tulia\Component\Theme\Customizer\Changeset\Changeset;

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
        $this->registerThemeConfiguration($root);
        $this->registerTranslationConfiguration($root);
        $this->registerMigrationConfiguration($root);

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

    private function registerThemeConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('theme')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('customizer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('builder')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('base_class')->defaultValue(BuilderInterface::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
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

    private function registerTranslationConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('translation')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('directory_list')->defaultValue([])->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerMigrationConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('migrations')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('paths')->defaultValue([])->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
