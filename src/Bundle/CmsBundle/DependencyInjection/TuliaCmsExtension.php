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

        $container->setParameter('cms.options.definitions', $this->validateOptionsValues($config['options']['definitions'] ?? []));

        // BodyClass
        $container->registerForAutoconfiguration(\Tulia\Cms\BodyClass\Ports\Domain\BodyClassCollectorInterface::class)
            ->addTag('body_class.collector');

        // Breadcrumbs
        $container->registerForAutoconfiguration(\Tulia\Cms\Breadcrumbs\Ports\Domain\BreadcrumbsResolverInterface::class)
            ->addTag('breadcrumbs.resolver');

        // Menus
        $container->registerForAutoconfiguration(\Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\MenuActionInterface::class)
            ->addTag('menu.action_chain');

        // Nodes
        $container->registerForAutoconfiguration(\Tulia\Cms\Node\Domain\WriteModel\ActionsChain\NodeActionInterface::class)
            ->addTag('node.action_chain');

        // Terms
        $container->registerForAutoconfiguration(\Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface::class)
            ->addTag('term.action_chain');
        $container->registerForAutoconfiguration(\Tulia\Cms\Taxonomy\Domain\Routing\Strategy\TaxonomyRoutingStrategyInterface::class)
            ->addTag('taxonomy.routing.strategy');
        $container->registerForAutoconfiguration(\Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistratorInterface::class)
            ->addTag('taxonomy.type.registrator');
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
