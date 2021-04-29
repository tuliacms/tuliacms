<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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
        }
    }
}
