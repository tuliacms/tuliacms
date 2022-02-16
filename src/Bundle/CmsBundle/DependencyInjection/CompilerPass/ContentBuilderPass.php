<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderRegistry;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecorator;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\ConstraintTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->addTaggedServices($container, ContentTypeRegistry::class, 'content_builder.content_type.provider', 'addProvider');
        $this->addTaggedServices($container, ContentTypeDecorator::class, 'content_builder.content_type.decorator', 'addDecorator');
        $this->addTaggedServices($container, LayoutTypeBuilderRegistry::class, 'content_builder.layout_type.builder', 'addBuilder');
        $this->addTaggedServices($container, ContentTypeRoutingStrategyRegistry::class, 'content_builder.content_type.routing_strategy', 'addStrategy');

        $registry = $container->getDefinition(FieldTypeMappingRegistry::class);
        foreach ($container->getParameter('cms.content_builder.data_types.mapping') as $type => $info) {
            $registry->addMethodCall('addMapping', [$type, $info]);
        }

        $registry = $container->getDefinition(FieldTypeBuilderRegistry::class);
        foreach ($container->findTaggedServiceIds('content_builder.data_types.builder') as $id => $info) {
            $registry->addMethodCall('addBuilder', [$id, new Reference($id)]);
        }

        $registry = $container->getDefinition(ConstraintTypeMappingRegistry::class);
        foreach ($container->getParameter('cms.content_builder.constraint_types.mapping') as $type => $info) {
            $registry->addMethodCall('addMapping', [$type, $info]);
        }
    }

    private function addTaggedServices(ContainerBuilder $container, string $serviceId, string $tagname, string $method)
    {
        $registry = $container->getDefinition($serviceId);

        foreach ($container->findTaggedServiceIds($tagname) as $id => $options) {
            if ($container->getDefinition($id)->isAbstract()) {
                continue;
            }

            $registry->addMethodCall($method, [new Reference($id)]);
        }
    }
}
