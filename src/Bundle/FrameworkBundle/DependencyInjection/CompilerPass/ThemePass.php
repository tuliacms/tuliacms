<?php

declare(strict_types=1);

namespace Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('theme.customizer.control') as $id => $tags) {
            $container->getDefinition($id)->addMethodCall('setTranslator', [new Reference(TranslatorInterface::class)]);
        }
    }
}
