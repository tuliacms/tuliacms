<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\LayoutTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition(NodeTypeRegistry::class);

        foreach ($container->findTaggedServiceIds('content_builder.node_type.provider') as $id => $options) {
            $registry->addMethodCall('addProvider', [new Reference($id)]);
        }

        $registry = $container->getDefinition(LayoutTypeRegistry::class);

        foreach ($container->findTaggedServiceIds('content_builder.layout_type.provider') as $id => $options) {
            $registry->addMethodCall('addProvider', [new Reference($id)]);
        }
    }
}
