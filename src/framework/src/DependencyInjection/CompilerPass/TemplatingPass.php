<?php

declare(strict_types=1);

namespace Tulia\Framework\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Component\CommandBus\Locator\LocatorInterface;
use Tulia\Component\Templating\ViewFilter\FilterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TemplatingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerViewFilters($container);
    }

    private function registerViewFilters(ContainerBuilder $container): void
    {
        if (! $container->has(FilterInterface::class)) {
            return;
        }

        $chain = $container->findDefinition(FilterInterface::class);

        foreach ($container->findTaggedServiceIds('templating.view_filter') as $id => $tags) {
            $chain->addMethodCall('addFilter', [new Reference($id)]);
        }
    }
}
