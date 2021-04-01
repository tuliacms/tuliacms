<?php

declare(strict_types=1);

namespace Tulia\Framework\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerLoaders($container);
        $this->registerExtensions($container);
    }

    private function registerExtensions(ContainerBuilder $container): void
    {
        if (! $container->has(Environment::class)) {
            return;
        }

        $chain = $container->findDefinition(Environment::class);

        foreach ($container->findTaggedServiceIds('twig.extension') as $id => $tags) {
            $chain->addMethodCall('addExtension', [new Reference($id)]);
        }
    }

    private function registerLoaders(ContainerBuilder $container): void
    {
        if (! $container->has(LoaderInterface::class)) {
            return;
        }

        $chain = $container->findDefinition(LoaderInterface::class);

        foreach ($container->findTaggedServiceIds('twig.loader') as $id => $tags) {
            $chain->addMethodCall('addLoader', [new Reference($id)]);
        }
    }
}
