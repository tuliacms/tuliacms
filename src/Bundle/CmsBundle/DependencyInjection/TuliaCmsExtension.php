<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaCmsExtension extends Extension
{
    public function getAlias(): string
    {
        return 'cms';
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return new Configuration();
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $paths = $config['content_blocks']['templating']['paths'];

        foreach ($paths as $key => $path) {
            $paths[$key] = rtrim($path, '/') . '/';
        }

        $container->setParameter('cms.content_blocks.templating.paths', $paths);
        $container->setParameter('cms.content_builder.content_type_entry.config', $config['content_building']['content_type_entry']);
        $container->setParameter('cms.content_builder.content_type.config', $config['content_building']['content_type']);
        $container->setParameter('cms.content_builder.data_types.mapping', $config['content_building']['data_types']['mapping']);
        $container->setParameter('cms.content_builder.constraint_types.mapping', $config['content_building']['constraint_types']['mapping']);
        $container->setParameter('cms.options.definitions', $this->validateOptionsValues($config['options']['definitions'] ?? []));
        $container->setParameter('cms.attributes.finder.types', $config['attributes']['finder']['types'] ?? []);

        // BodyClass
        $container->registerForAutoconfiguration(\Tulia\Cms\BodyClass\Collector\BodyClassCollectorInterface::class)
            ->addTag('body_class.collector');

        // Breadcrumbs
        $container->registerForAutoconfiguration(\Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsResolverInterface::class)
            ->addTag('breadcrumbs.resolver');

        // Dashboard
        $container->registerForAutoconfiguration(
            \Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollector::class)
            ->addTag('dashboard.tiles_collector');
        $container->registerForAutoconfiguration(
            \Tulia\Cms\Homepage\UserInterface\Web\Backend\Widgets\DashboardWidgetInterface::class)
            ->addTag('dashboard.widget');
        $container->registerForAutoconfiguration(\Tulia\Cms\BackendMenu\Domain\Builder\BuilderInterface::class)
            ->addTag('backend_menu.builder');

        // EditLinks
        $container->registerForAutoconfiguration(\Tulia\Cms\EditLinks\Service\EditLinksCollectorInterface::class)
            ->addTag('edit_links.collector');

        // FrontendToolbar
        $container->registerForAutoconfiguration(\Tulia\Cms\FrontendToolbar\Ports\Domain\Links\LinksCollectorInterface::class)
            ->addTag('frontend_toolbar.links.provider');

        // Menus
        $container->registerForAutoconfiguration(\Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\MenuActionInterface::class)
            ->addTag('menu.action_chain');
        $container->registerForAutoconfiguration(\Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface::class)
            ->addTag('menu.builder.type_registrator');

        // Nodes
        $container->registerForAutoconfiguration(\Tulia\Cms\Node\Domain\WriteModel\ActionsChain\NodeActionInterface::class)
            ->addTag('node.action_chain');
        $container->registerForAutoconfiguration(\Tulia\Cms\Node\Domain\NodeFlag\NodeFlagProviderInterface::class)
            ->addTag('node.flag_provider');

        // Terms
        $container->registerForAutoconfiguration(\Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface::class)
            ->addTag('term.action_chain');

        // ContentBuilder
        $container->registerForAutoconfiguration(\Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface::class)
            ->addTag('content_builder.content_type.decorator');
        $container->registerForAutoconfiguration(\Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeProviderInterface::class)
            ->addTag('content_builder.content_type.provider');
        $container->registerForAutoconfiguration(\Tulia\Cms\ContentBuilder\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyInterface::class)
            ->addTag('content_builder.content_type.routing_strategy');
        $container->registerForAutoconfiguration(\Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderInterface::class)
            ->addTag('content_builder.layout_type.builder');
        $container->registerForAutoconfiguration(\Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface::class)
            ->addTag('content_builder.data_types.builder')
            ->setLazy(true);

        // Shortcode
        $container->registerForAutoconfiguration(\Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface::class)
            ->addTag('shortcode.compiler');
    }

    protected function validateOptionsValues(array $definitions): array
    {
        foreach ($definitions as $name => $definition) {
            if ($definition['type'] === 'array' && \is_array($definition['value']) === false) {
                throw new \InvalidArgumentException(sprintf('Default value of %s option must be an array.', $name));
            }
            if ($definition['type'] === 'boolean' && \is_bool($definition['value']) === false) {
                throw new \InvalidArgumentException(sprintf('Default value of %s option must be a boolean.', $name));
            }
            if ($definition['type'] === 'number' && \is_numeric($definition['value']) === false) {
                throw new \InvalidArgumentException(sprintf('Default value of %s option must be numeric.', $name));
            }
        }

        return $definitions;
    }
}
