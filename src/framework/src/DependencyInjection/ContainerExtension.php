<?php

declare(strict_types=1);

namespace Tulia\Framework\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerExtension extends Extension
{
    public function getAlias(): string
    {
        return 'framework';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $configs = $this->processConfiguration($configuration, $configs);

        //$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        //$loader->load('services.yaml');
    }
}
