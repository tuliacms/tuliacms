<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin\PluginInterface;
use Tulia\Component\Templating\ViewFilter\FilterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaCmsExtension extends FrameworkExtension
{
    public function getAlias(): string
    {
        return 'framework';
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return new Configuration($container->getParameter('kernel.debug'));
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        parent::load($configs, $container);

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('framework.assetter.assets', $config['assetter']['assets'] ?? []);
        $container->setParameter('framework.assets.public_paths', $config['public_paths'] ?? []);
        $container->setParameter('framework.twig.loader.array.templates', $this->prepareTwigArrayLoaderTemplates($config['twig']['loader']['array']['templates'] ?? []));
        $container->setParameter('framework.templating.paths', $this->prepareTemplatingPaths($config['templating']['paths'] ?? []));
        $container->setParameter('framework.templating.namespace_overwrite', $config['templating']['namespace_overwrite'] ?? []);
        $container->setParameter('framework.theme.customizer.builder.base_class', $config['theme']['customizer']['builder']['base_class']);

        $this->registerViewFilters($container);

        $container->registerForAutoconfiguration(AbstractFinder::class)
            ->addTag('finder');
        $container->registerForAutoconfiguration(PluginInterface::class)
            ->addTag('finder.plugin');
    }

    private function prepareTemplatingPaths(array $paths): array
    {
        $prepared = [];

        foreach ($paths as $path) {
            $prepared["@{$path['name']}"] = $path['path'];
        }

        return $prepared;
    }

    private function prepareTwigArrayLoaderTemplates(array $source): array
    {
        $output = [];

        foreach ($source as $name => $data) {
            $output[$name] = $data['template'];
        }

        return $output;
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
