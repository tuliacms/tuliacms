<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Tulia\Cms\ContentBuilder\Model\NodeType\NodeType;
use Tulia\Cms\ContentBuilder\Model\NodeType\NodeTypeRegistry;

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

        $container->setParameter('cms.content_builder.node_types', $config['content_building']['node_types']);
        $container->setParameter('cms.content_builder.layout_types', $config['content_building']['layout_types']);
        $container->setParameter('cms.options.definitions', $this->validateOptionsValues($config['options']['definitions'] ?? []));

        // BodyClass
        $container->registerForAutoconfiguration(\Tulia\Cms\BodyClass\Ports\Domain\BodyClassCollectorInterface::class)
            ->addTag('body_class.collector');

        // Breadcrumbs
        $container->registerForAutoconfiguration(\Tulia\Cms\Breadcrumbs\Ports\Domain\BreadcrumbsResolverInterface::class)
            ->addTag('breadcrumbs.resolver');

        // Dashboard
        $container->registerForAutoconfiguration(\Tulia\Cms\Dashboard\Ports\Domain\Tiles\DashboardTilesCollector::class)
            ->addTag('dashboard.tiles_collector');
        $container->registerForAutoconfiguration(\Tulia\Cms\Dashboard\Ports\Domain\Widgets\DashboardWidgetInterface::class)
            ->addTag('dashboard.widget');

        // EditLinks
        $container->registerForAutoconfiguration(\Tulia\Cms\EditLinks\Ports\Domain\EditLinksCollectorInterface::class)
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
        $container->registerForAutoconfiguration(\Tulia\Cms\Node\Domain\NodeType\NodeTypeStorageInterface::class)
            ->addTag('node.type.storage');

        // Terms
        $container->registerForAutoconfiguration(\Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface::class)
            ->addTag('term.action_chain');
        $container->registerForAutoconfiguration(\Tulia\Cms\Taxonomy\Domain\Routing\Strategy\TaxonomyRoutingStrategyInterface::class)
            ->addTag('taxonomy.routing.strategy');
        $container->registerForAutoconfiguration(\Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistratorInterface::class)
            ->addTag('taxonomy.type.registrator');

        // ContentBuilder
        $container->registerForAutoconfiguration(\Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeProviderInterface::class)
            ->addTag('content_builder.node_type.provider');
        $container->registerForAutoconfiguration(\Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\LayoutTypeProviderInterface::class)
            ->addTag('content_builder.layout_type.provider');
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
