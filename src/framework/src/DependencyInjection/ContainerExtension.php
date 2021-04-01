<?php

declare(strict_types=1);

namespace Tulia\Framework\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;

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

        $container->setParameter('framework.assetter.assets', $configs['assetter']['assets'] ?? []);
        $container->setParameter('framework.twig.loader.array.templates', $this->prepareTwigArrayLoaderTemplates($configs['twig']['loader']['array']['templates'] ?? []));
        $container->setParameter('framework.twig.loader.filesystem.paths', $this->prepareTwigFilesystemLoaderPaths($configs['twig']['loader']['filesystem']['paths'] ?? []));
        $container->setParameter('framework.twig.layout.themes', $configs['twig']['layout']['themes'] ?? []);
        $container->setParameter('framework.templating.namespace_overwrite', $configs['templating']['namespace_overwrite'] ?? []);
        $container->setParameter('framework.templating.paths', $configs['templating']['paths'] ?? []);
        $container->setParameter('framework.theme.customizer.builder.base_class', $configs['theme']['customizer']['builder']['base_class']);
        $container->setParameter('framework.theme.changeset.base_class', $configs['theme']['changeset']['base_class']);
        $container->setParameter('framework.translation.directory_list', $configs['translation']['directory_list']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    private function prepareTwigArrayLoaderTemplates(array $source): array
    {
        $output = [];

        foreach ($source as $name => $data) {
            $output[$name] = $data['template'];
        }

        return $output;
    }

    private function prepareTwigFilesystemLoaderPaths(array $source): array
    {
        $output = [];

        foreach ($source as $name => $data) {
            $output[$name] = $data['path'];
        }

        return $output;
    }
}
