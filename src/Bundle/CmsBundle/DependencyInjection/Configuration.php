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
                                                ->beforeNormalization()
                                                    ->always(function ($configs) {
                                                        /*foreach ($configs as $config) {
                                                            if ($config['type'] === 'choice' && $config['choices'] === [] ) {

                                                            }
                                                        }*/

                                                        return $configs;
                                                    })
                                                ->end()
                                                ->arrayPrototype()
                                                    ->addDefaultsIfNotSet()
                                                    ->children()
                                                        ->scalarNode('type')->defaultValue('string')->end()
                                                        ->scalarNode('label')->isRequired()->end()
                                                        ->scalarNode('help_text')->defaultNull()->end()
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
                        ->arrayNode('node_types')
                            ->beforeNormalization()
                                ->always(function ($types) {
                                    foreach ($types as $name => $type) {
                                        foreach ($type['fields'] as $fieldName => $field) {
                                            if (isset($field['taxonomy']) && $field['type'] !== 'taxonomy') {
                                                throw new LogicException(sprintf('Field "%s" cannot have "taxonomy" option set, if field is not a "taxonomy" type.', $fieldName));
                                            }
                                        }
                                    }

                                    return $types;
                                })
                            ->end()
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->variableNode('icon')->defaultValue('fas fa-circle')->end()
                                    ->variableNode('controller')->defaultValue('Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node::show')->end()
                                    // Any node of this type can be reached through it's own route (using it's slug)?
                                    ->booleanNode('is_routable')->defaultTrue()->end()
                                    // Name of the field, where is stored routable taxonomy relation.
                                    // If not provided, slug to this node type will not be generated with taxonomy path.
                                    ->scalarNode('routable_taxonomy_field')->defaultNull()->end()
                                    // If true, nodes can be created as hierarchical tree, with parents and childs.
                                    // Also those node's paths, will be created with all ascendants.
                                    ->booleanNode('is_hierarchical')->defaultFalse()->end()
                                    // Layout name with defines where each field should be showed on admin page.
                                    ->scalarNode('layout')->isRequired()->end()
                                    // Fields for given type
                                    ->arrayNode('fields')
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('label')
                                                    ->defaultNull()
                                                    ->beforeNormalization()
                                                        ->always(function ($v) {
                                                            if ($v === false) {
                                                                return '';
                                                            }

                                                            return $v;
                                                        })
                                                    ->end()
                                                ->end()
                                                ->scalarNode('type')->defaultNull()->end()
                                                ->booleanNode('multilingual')->defaultFalse()->end()
                                                ->booleanNode('multiple')->defaultFalse()->end()
                                                ->scalarNode('taxonomy')->defaultNull()->end()
                                                ->arrayNode('constraints')
                                                    ->arrayPrototype()
                                                        ->children()
                                                            ->scalarNode('name')->isRequired()->end()
                                                            ->scalarNode('flags')->defaultValue('')->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('taxonomy_types')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->variableNode('controller')->defaultValue('Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\Controller\Term::show')->end()
                                    ->booleanNode('is_routable')->defaultTrue()->end()
                                    ->booleanNode('is_hierarchical')->defaultFalse()->end()
                                    ->scalarNode('routing_strategy')->defaultValue('simple')->end()
                                    // Layout name with defines where each field should be showed on admin page.
                                    ->scalarNode('layout')->isRequired()->end()
                                    // Fields for given type
                                    ->arrayNode('fields')
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('label')
                                                    ->defaultNull()
                                                    ->beforeNormalization()
                                                        ->always(function ($v) {
                                                            if ($v === false) {
                                                                return '';
                                                            }

                                                            return $v;
                                                        })
                                                    ->end()
                                                ->end()
                                                ->scalarNode('type')->defaultNull()->end()
                                                ->booleanNode('multilingual')->defaultFalse()->end()
                                                ->booleanNode('multiple')->defaultFalse()->end()
                                                ->arrayNode('constraints')
                                                    ->arrayPrototype()
                                                        ->children()
                                                            ->scalarNode('name')->isRequired()->end()
                                                            ->scalarNode('flags')->defaultValue('')->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
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
                                    // Builder service, which implements LayoutBuilderInterface.
                                    // Builder is responsible for render layout with defined fields and sections for form.
                                    ->scalarNode('builder')
                                        ->defaultValue(\Tulia\Cms\ContentBuilder\Infrastructure\Presentation\TwigLayoutTypeBuilder::class)
                                        ->validate()
                                            ->ifTrue(static function ($v) {
                                                return ! $v instanceof LayoutTypeBuilderInterface;
                                            })
                                            ->thenInvalid('LayoutBuilder must be instance of ' . LayoutTypeBuilderInterface::class . ' %s given.')
                                        ->end()
                                    ->end()
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
                                                            ->scalarNode('interior')->defaultValue('default')->end()
                                                            ->booleanNode('active')->defaultFalse()->end()
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
