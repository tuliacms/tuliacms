<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection;

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
}
