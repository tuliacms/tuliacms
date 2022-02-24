<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionInterface;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DomainActionChainPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerActionsChain($container);
    }

    private function registerActionsChain(ContainerBuilder $container): void
    {
        $chain = $container->findDefinition(AggregateActionsChainInterface::class);
        $taggedServices = $container->findTaggedServiceIds('cms.domain.action_chain');

        /** @var AggregateActionInterface $id */

        foreach ($taggedServices as $id => $tags) {
            foreach ($id::listen() as $action => $priority) {
                $chain->addMethodCall('addAction', [
                    new Reference($id),
                    (string) $action,
                    $id::supports(),
                    (int) $priority
                ]);
            }
        }
    }
}
