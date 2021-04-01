<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Tulia\Cms\Platform\Infrastructure\Framework\Kernel\TuliaKernel;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerExtension extends Extension
{
    public function getAlias(): string
    {
        return 'platform';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        foreach (TuliaKernel::getConfigDirs() as $dir) {
            $loader = new YamlFileLoader($container, new FileLocator($dir));
            $loader->load('services.yaml');
        }
    }
}
