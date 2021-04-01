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
        //dump($configs);exit;

        $container->setParameter('framework.assetter.assets', $configs['assetter']['assets'] ?? []);
        $container->setParameter('framework.twig.loader.array.templates', $configs['twig']['loader']['array']['templates'] ?? []);
        $container->setParameter('framework.twig.loader.filesystem.paths', $configs['twig']['loader']['filesystem']['paths'] ?? []);
        $container->setParameter('framework.templating.paths', $configs['templating']['paths'] ?? []);
        $container->setParameter('framework.templating.namespace_overwrite', $configs['templating']['namespace_overwrite'] ?? []);

        //$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        //$loader->load('services.yaml');
    }
}
