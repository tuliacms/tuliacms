<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
                        ->arrayNode('node_types')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->variableNode('controller')->defaultValue('Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node::show')->end()
                                    // Any node of this type can be reached through it's own route (using it's slug)?
                                    ->booleanNode('is_routable')->defaultTrue()->end()
                                    // Name of the field, where is stored routable taxonomy relation.
                                    // If not provided, slug to this node type will not be generated with taxonomy path.
                                    ->scalarNode('routable_taxonomy_field')->defaultNull()->end()
                                    ->scalarNode('translation_domain')->defaultValue('page')->end()
                                    // If true, nodes can be created as hierarchical tree, with parents and childs.
                                    // Also those node's paths, will be created with all ascendants.
                                    ->booleanNode('is_hierarchical')->defaultFalse()->end()
                                    // Layout name with defines where each field should be showed on admin page.
                                    ->scalarNode('layout')->defaultValue('default')->end()
                                    // Fields for given type
                                    ->arrayNode('fields')
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('label')->defaultNull()->end()
                                                ->scalarNode('type')->defaultNull()->end()
                                                ->booleanNode('is_title')->defaultFalse()->end()
                                                ->booleanNode('is_slug')->defaultFalse()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('layout_types')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('label')->defaultNull()->end()
                                    ->scalarNode('translation_domain')->defaultNull()->end()
                                    // Builder service, which implements LayoutBuilderInterface.
                                    // Builder is responsible for render layout with defined fields and sections for form.
                                    ->scalarNode('builder')->defaultValue('LayoutBuilderInterface')->end()
                                    ->arrayNode('sections')
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->arrayNode('groups')
                                                    ->useAttributeAsKey('name')
                                                    ->arrayPrototype()
                                                        ->addDefaultsIfNotSet()
                                                        ->children()
                                                            ->scalarNode('label')->isRequired()->end()
                                                            ->arrayNode('fields')->scalarPrototype()->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
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
}
