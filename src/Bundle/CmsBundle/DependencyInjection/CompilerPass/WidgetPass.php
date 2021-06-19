<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tulia\Component\Templating\Twig\Loader\AdvancedFilesystemLoader;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $loader = $container->findDefinition(AdvancedFilesystemLoader::class);
        $widgets = $container->findTaggedServiceIds('widget');

        foreach ($widgets as $id => $tags) {
            $definition = $container->getDefinition($id);
            $className = $definition->getClass();

            $loader->addMethodCall('setPath', [
                '@widget/' . str_replace('.', '/', $className::getId()),
                \dirname((new ReflectionClass($className))->getFileName())
            ]);
        }
    }
}
