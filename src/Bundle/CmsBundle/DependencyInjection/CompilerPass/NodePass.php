<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\ActionsChainInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerActionsChain($container);
    }

    private function registerActionsChain(ContainerBuilder $container): void
    {
        $chain = $container->findDefinition(ActionsChainInterface::class);
        $taggedServices = $container->findTaggedServiceIds('node.action_chain');

        foreach ($taggedServices as $id => $tags) {
            foreach ($id::supports() as $name => $priority) {
                $chain->addMethodCall('addAction', [new Reference($id), (string) $name, (int) $priority]);
            }
        }
    }
}
