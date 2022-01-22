<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cms');
        $root = $treeBuilder->getRootNode();

        $this->registerOptionsConfiguration($root);
        $this->registerContentBuildingConfiguration($root);

        return $treeBuilder;
    }

    private function registerOptionsConfiguration(NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('options')
                    ->children()
                        ->arrayNode('definitions')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->variableNode('value')->defaultNull()->end()
                                    ->booleanNode('multilingual')->defaultFalse()->end()
                                    ->booleanNode('autoload')->defaultFalse()->end()
                                    ->scalarNode('type')
                                        ->defaultValue('scalar')
                                            ->validate()
                                                ->ifNotInArray(['scalar', 'boolean', 'number', 'array'])
                                                ->thenInvalid('Invalid option type %s. Allowed: scalar, array.')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerContentBuildingConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('content_building')
                    ->children()
                        ->arrayNode('constraint_types')
                            ->children()
                                ->append($this->buildConstraintsNode('mapping'))
                            ->end()
                        ->end()
                        ->arrayNode('data_types')
                            ->children()
                                ->arrayNode('mapping')
                                    ->arrayPrototype()
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('flags')->scalarPrototype()->defaultValue([])->end()->end()
                                            ->scalarNode('label')->isRequired()->end()
                                            ->scalarNode('classname')->isRequired()->end()
                                            ->scalarNode('builder')->defaultNull()->end()
                                            ->arrayNode('constraints')->scalarPrototype()->defaultValue([])->end()->end()
                                            ->arrayNode('configuration')
                                                ->arrayPrototype()
                                                    ->addDefaultsIfNotSet()
                                                    ->children()
                                                        ->scalarNode('type')->defaultValue('string')->end()
                                                        ->scalarNode('label')->isRequired()->end()
                                                        ->scalarNode('help_text')->defaultNull()->end()
                                                        ->scalarNode('placeholder')->defaultNull()->end()
                                                        ->scalarNode('required')->defaultFalse()->end()
                                                        ->scalarNode('choices_provider')->defaultNull()->end()
                                                        ->arrayNode('choices')
                                                            ->arrayPrototype()
                                                                ->children()
                                                                    ->scalarNode('value')->isRequired()->end()
                                                                    ->scalarNode('label')->defaultValue('')->end()
                                                                ->end()
                                                            ->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->append($this->buildConstraintsNode('custom_constraints'))
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('content_type')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('controller')->isRequired()->end()
                                    ->scalarNode('layout_builder')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildConstraintsNode(string $nodeName)
    {
        $treeBuilder = new TreeBuilder($nodeName);

        return $treeBuilder->getRootNode()
            ->arrayPrototype()
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('classname')->isRequired()->end()
                    ->scalarNode('label')->isRequired()->end()
                    ->scalarNode('help_text')->defaultNull()->end()
                    ->arrayNode('modificators')
                        ->arrayPrototype()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->isRequired()->end()
                                ->scalarNode('label')->isRequired()->end()
                                ->scalarNode('value')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
