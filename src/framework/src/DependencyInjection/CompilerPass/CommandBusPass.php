<?php

declare(strict_types=1);

namespace Tulia\Framework\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Component\CommandBus\Locator\LocatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CommandBusPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (! $container->has(LocatorInterface::class)) {
            return;
        }

        $definition = $container->findDefinition(LocatorInterface::class);

        $taggedServices = $container->findTaggedServiceIds('command_bus.handler');

        foreach ($taggedServices as $id => $tags) {
            if (isset($tag['handles']) === false) {
                throw new \InvalidArgumentException(sprintf('Missing "handles" option for tagged service "%s".', $id));
            }

            $definition->addMethodCall('addHandler', [$tag['handles'], new Reference($id)]);
        }
    }
}
