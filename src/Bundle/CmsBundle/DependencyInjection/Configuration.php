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
        $this->registerContentBlockConfiguration($root);
        $this->registerAttributesConfiguration($root);
        $this->registerWidgetsConfiguration($root);
        $this->registerFilemanagerConfiguration($root);
        $this->registerImporterConfiguration($root);

        return $treeBuilder;
    }

    private function registerOptionsConfiguration(ArrayNodeDefinition $root): void
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

    private function registerAttributesConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('attributes')
                    ->children()
                        ->arrayNode('finder')
                            ->children()
                                ->arrayNode('types')
                                    ->arrayPrototype()
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('scopes')->scalarPrototype()->defaultValue([])->end()->end()
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
                                            ->scalarNode('handler')->defaultNull()->end()
                                            ->arrayNode('constraints')->scalarPrototype()->defaultValue([])->end()->end()
                                            ->arrayNode('exclude_for_types')->scalarPrototype()->defaultValue([])->end()->end()
                                            ->arrayNode('only_for_types')->scalarPrototype()->defaultValue([])->end()->end()
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
                                    ->scalarNode('controller')->defaultValue('')->end()
                                    ->scalarNode('layout_builder')->isRequired()->end()
                                    ->booleanNode('multilingual')->defaultTrue()->end()
                                    ->booleanNode('configurable')->defaultTrue()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('content_type_entry')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('type')->isRequired()->end()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->scalarNode('icon')->defaultNull()->end()
                                    ->scalarNode('controller')->defaultNull()->end()
                                    ->booleanNode('is_routable')->defaultFalse()->end()
                                    ->booleanNode('is_hierarchical')->defaultFalse()->end()
                                    ->scalarNode('routing_strategy')->defaultNull()->end()
                                    ->arrayNode('layout')
                                        ->children()
                                            ->arrayNode('sections')
                                                ->arrayPrototype()
                                                ->children()
                                                    ->arrayNode('groups')
                                                        ->arrayPrototype()
                                                            ->children()
                                                                ->scalarNode('name')->isRequired()->end()
                                                                ->booleanNode('active')->defaultFalse()->end()
                                                                ->integerNode('order')->defaultValue(1)->end()
                                                                ->append($this->buildContentTypeFieldsNode('fields'))
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
            ->end()
        ;
    }

    private function registerContentBlockConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('content_blocks')
                    ->children()
                        ->arrayNode('templating')
                            ->children()
                                ->arrayNode('paths')->scalarPrototype()->defaultValue([])->end()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerWidgetsConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('widgets')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('classname')->isRequired()->end()
                            ->scalarNode('name')->isRequired()->end()
                            ->scalarNode('views')->isRequired()->end()
                            ->scalarNode('description')->defaultNull()->end()
                            ->scalarNode('translation_domain')->defaultNull()->end()
                            ->append($this->buildContentTypeFieldsNode('fields'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerFilemanagerConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('filemanager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('image_sizes')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->integerNode('width')->defaultNull()->end()
                                    ->integerNode('height')->defaultNull()->end()
                                    ->scalarNode('mode')->defaultValue('fit')->end()
                                    ->scalarNode('translation_domain')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerImporterConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('importer')
                    ->children()
                        ->arrayNode('objects')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('importer')->defaultNull()->end()
                                    ->arrayNode('mapping')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('type')->defaultValue('string')->end()
                                                ->booleanNode('required')->defaultTrue()->end()
                                                ->variableNode('default_value')->defaultNull()->end()
                                                ->scalarNode('collection_of')->defaultNull()->end()
                                            ->end()
                                        ->end()
                                        ->validate()
                                            ->always(function (array $fields) {
                                                foreach ($fields as $key => $field) {
                                                    if ($fields[$key]['collection_of']) {
                                                        $fields[$key]['type'] = $fields[$key]['collection_of'];
                                                        $fields[$key]['collection'] = true;
                                                    } else {
                                                        $fields[$key]['collection'] = false;
                                                    }

                                                    unset($fields[$key]['collection_of']);
                                                }

                                                return $fields;
                                            })
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

    private function buildConstraintsNode(string $nodeName): NodeDefinition
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

    private function buildContentTypeFieldsNode(string $nodeName): NodeDefinition
    {
        $treeBuilder = new TreeBuilder($nodeName);

        return $treeBuilder->getRootNode()
            ->arrayPrototype()
                ->children()
                    ->scalarNode('type')->isRequired()->end()
                    ->scalarNode('name')->isRequired()->end()
                    ->scalarNode('translation_domain')->defaultValue('content_builder.field')->end()
                    ->booleanNode('is_multilingual')->defaultFalse()->end()
                    ->scalarNode('parent')->defaultNull()->end()
                    ->arrayNode('configuration')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('code')->isRequired()->end()
                                ->scalarNode('value')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('constraints')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('code')->isRequired()->end()
                                ->arrayNode('modificators')
                                    ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('code')->isRequired()->end()
                                            ->scalarNode('value')->isRequired()->end()
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
