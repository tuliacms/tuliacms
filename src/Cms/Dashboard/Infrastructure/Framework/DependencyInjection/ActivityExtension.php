<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Infrastructure\Framework\DependencyInjection;

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\DependencyInjection\Extension\AbstractExtension;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityExtension extends AbstractExtension
{
    public function compile(ContainerBuilderInterface $container): void
    {
        $widgets = $container->getTaggedDefinitions('dashboard.widget');

        foreach ($widgets as $id => $data) {
            $widget = $container->getDefinition($id);

            $widget['calls'][] = call('setTemplating', [service(EngineInterface::class)]);

            $container->setDefinition($id, $widget['classname'], $widget);
        }
    }
}
