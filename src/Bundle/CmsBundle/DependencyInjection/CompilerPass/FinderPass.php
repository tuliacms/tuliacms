<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Plugin\PluginRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class FinderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('finder') as $id => $tags) {
            $definition = $container->getDefinition($id);
            $definition->addMethodCall('setEventDispatcher', [new Reference(EventDispatcherInterface::class)]);
            $definition->addMethodCall('setPluginsRegistry', [new Reference(PluginRegistry::class)]);
        }

        $pluginRegistry = $container->getDefinition(PluginRegistry::class);

        foreach ($container->findTaggedServiceIds('finder.plugin') as $id => $tags) {
            $pluginRegistry->addMethodCall('addPlugin', [new Reference($id)]);
        }
    }
}
