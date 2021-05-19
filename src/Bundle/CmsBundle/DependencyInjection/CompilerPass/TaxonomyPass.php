<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TermActionsChainInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerActionsChain($container);
    }

    private function registerActionsChain(ContainerBuilder $container): void
    {
        $chain = $container->findDefinition(TermActionsChainInterface::class);
        $taggedServices = $container->findTaggedServiceIds('term.action_chain');

        foreach ($taggedServices as $id => $tags) {
            foreach ($id::supports() as $name => $priority) {
                $chain->addMethodCall('addAction', [new Reference($id), (string) $name, (int) $priority]);
            }
        }
    }
}
