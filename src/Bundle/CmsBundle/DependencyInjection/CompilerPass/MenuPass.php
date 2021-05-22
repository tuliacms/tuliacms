<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\MenuActionsChainInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerActionsChain($container);
    }

    private function registerActionsChain(ContainerBuilder $container): void
    {
        $chain = $container->findDefinition(MenuActionsChainInterface::class);
        $taggedServices = $container->findTaggedServiceIds('menu.action_chain');

        foreach ($taggedServices as $id => $tags) {
            foreach ($id::supports() as $name => $priority) {
                $chain->addMethodCall('addAction', [new Reference($id), (string) $name, (int) $priority]);
            }
        }
    }
}
