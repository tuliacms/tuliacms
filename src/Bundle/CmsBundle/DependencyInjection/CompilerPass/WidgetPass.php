<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Widget\Domain\Catalog\Registry\WidgetRegistryInterface;
use Tulia\Component\Templating\Twig\Loader\AdvancedFilesystemLoader;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $loader = $container->findDefinition(AdvancedFilesystemLoader::class);
        $registry = $container->findDefinition(WidgetRegistryInterface::class);
        $widgets = $container->findTaggedServiceIds('cms.widget');
        $widgetsInfo = $container->getParameter('cms.widgets');

        foreach ($widgetsInfo as $id => $info) {
            if (isset($widgets[$info['classname']]) === false) {
                throw new \LogicException(sprintf(
                    'Cannot find "%s" service for "%s" widget. Please verify that class exists and is registered and widget.',
                    $info['classname'],
                    $id
                ));
            }

            $info['id'] = $id;

            $registry->addMethodCall('addWidget', [$info, new Reference($info['classname'])]);

            $loader->addMethodCall('setPath', [
                '@widget/' . str_replace('.', '/', $id),
                $info['views']
            ]);
        }
    }
}
