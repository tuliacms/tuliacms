<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\ConstraintTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeRegistry;
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

        $registry = $container->getDefinition(LayoutTypeBuilderRegistry::class);

        foreach ($container->findTaggedServiceIds('content_builder.layout_type.builder') as $id => $options) {
            $registry->addMethodCall('addBuilder', [new Reference($id)]);
        }

        $registry = $container->getDefinition(FieldTypeMappingRegistry::class);

        foreach ($container->getParameter('cms.content_builder.field_types.mapping') as $type => $info) {
            $registry->addMethodCall('addMapping', [$type, $info]);
        }

        $registry = $container->getDefinition(ConstraintTypeMappingRegistry::class);

        foreach ($container->getParameter('cms.content_builder.constraints_types.mapping') as $type => $info) {
            $registry->addMethodCall('addMapping', [$type, $info]);
        }
    }
}
