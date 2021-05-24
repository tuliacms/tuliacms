<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DashboardPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $widgets = $container->findTaggedServiceIds('dashboard.widget');

        foreach ($widgets as $id => $data) {
            $widget = $container->getDefinition($id);
            $widget->addMethodCall('setTemplating', [new Reference(EngineInterface::class)]);
        }
    }
}
