<?php

declare(strict_types=1);

namespace Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Theme\Configuration\Configuration;
use Tulia\Component\Theme\Configuration\ConfigurationRegistry;
use Tulia\Component\Theme\Customizer\Builder\Structure\StructureRegistry;
use Tulia\Component\Theme\Customizer\Changeset\PredefinedChangesetRegistry;

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

        $configurationRegistry = $container->getDefinition(ConfigurationRegistry::class);
        $structureRegistry = $container->getDefinition(StructureRegistry::class);
        $predefinedChangesetsRegistry = $container->getDefinition(PredefinedChangesetRegistry::class);

        foreach ($container->getParameter('framework.themes.configuration') as $theme => $config) {
            if (isset($config['configuration']['base'])) {
                $this->processThemeConfiguration($container, $configurationRegistry, 'base', $theme, $config['configuration']['base']);
            }
            if (isset($config['configuration']['customizer'])) {
                $this->processThemeConfiguration($container, $configurationRegistry, 'customizer', $theme, $config['configuration']['customizer']);
            }
            if (isset($config['customizer']['builder'])) {
                $structureRegistry->addMethodCall('addForTheme', [$theme, $this->resolveCustomizerStructure($config['customizer']['builder'], $config['translation_domain'])]);
            }
            if (isset($config['customizer']['changesets'])) {
                foreach ($config['customizer']['changesets'] as $key => $changeset) {
                    $config['customizer']['changesets'][$key]['translation_domain'] = $config['translation_domain'];
                }

                $predefinedChangesetsRegistry->addMethodCall('addForTheme', [$theme, $config['customizer']['changesets']]);
            }
        }
    }

    private function processThemeConfiguration(ContainerBuilder $container, Definition $registry, string $group, string $theme, array $config): void
    {
        $service = new Definition(Configuration::class);
        $service->setShared(true);

        if (isset($config['assets'])) {
            foreach ($config['assets'] as $asset) {
                $service->addMethodCall('add', ['asset', $asset]);
            }
        }
        if (isset($config['colors'])) {
            foreach ($config['colors'] as $code => $color) {
                $service->addMethodCall('add', ['color', $code, $color['value']]);
            }
        }
        if (isset($config['widget_spaces'])) {
            foreach ($config['widget_spaces'] as $code => $space) {
                $service->addMethodCall('add', ['widget_space', $code, $space['label']]);
            }
        }
        if (isset($config['widget_styles'])) {
            foreach ($config['widget_styles'] as $code => $space) {
                $service->addMethodCall('add', ['widget_style', $code, $space['label']]);
            }
        }
        if (isset($config['image_sizes'])) {
            foreach ($config['image_sizes'] as $code => $size) {
                $service->addMethodCall('add', ['widget_style', $code, $size]);
            }
        }

        $serviceName = sprintf('theme.configuration.%s.%s', $theme, $group);

        $container->setDefinition($serviceName, $service);
        $registry->addMethodCall('addConfiguration', [$theme, $group, new Reference($serviceName)]);
    }

    private function resolveCustomizerStructure(array $source, string $translationDomain): array
    {
        $structure = [];

        foreach ($source as $code => $section) {
            $controls = [];

            foreach ($section['controls'] as $controlCode => $control) {
                $control['code'] = $controlCode;
                $control['options'] = $control['control_options'];

                unset($control['control_options']);

                $controls[] = $control;
            }

            $section['code'] = $code;
            $section['translation_domain'] = $translationDomain;
            $section['controls'] = $controls;

            $structure[] = $section;
        }

        return $structure;
    }
}
