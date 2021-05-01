<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Component\Routing\ChainRouterInterface;
use Tulia\Component\Routing\Website\WebsiteRegistryFactory;

/**
 * @author Adam Banaszkiewicz
 */
class RoutingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $chain = $container->findDefinition(ChainRouterInterface::class);

        foreach ($container->findTaggedServiceIds('routing_chain.router') as $id => $tags) {
            $chain->addMethodCall('add', [new Reference($id)]);
        }
    }
}
