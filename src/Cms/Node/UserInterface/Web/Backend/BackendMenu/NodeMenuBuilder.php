<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\BackendMenu;

use Tulia\Cms\BackendMenu\Ports\Domain\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Registry\ItemRegistryInterface;
use Tulia\Cms\Node\Domain\NodeType\Enum\ParametersEnum;
use Tulia\Cms\Node\Domain\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Domain\NodeType\RegistryInterface as NodeRegistry;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface as TaxonomyRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuBuilder implements BuilderInterface
{
    protected BuilderHelperInterface $helper;

    protected NodeRegistry $nodeRegistry;

    protected TaxonomyRegistry $taxonomyRegistry;

    public function __construct(
        BuilderHelperInterface $helper,
        NodeRegistry $nodeRegistry,
        TaxonomyRegistry $taxonomyRegistry
    ) {
        $this->helper = $helper;
        $this->nodeRegistry = $nodeRegistry;
        $this->taxonomyRegistry = $taxonomyRegistry;
    }

    /**
     * @param ItemRegistryInterface $registry
     */
    public function build(ItemRegistryInterface $registry): void
    {
        foreach ($this->nodeRegistry->getRegisteredTypesNames() as $name) {
            $this->registerNodeType($registry, $this->nodeRegistry->getType($name));
        }
    }

    /**
     * @param ItemRegistryInterface $registry
     * @param NodeTypeInterface     $type
     */
    private function registerNodeType(ItemRegistryInterface $registry, NodeTypeInterface $type): void
    {
        $root = 'node_' . $type->getType();

        $registry->add($root, [
            'label'    => $this->helper->trans('node', [], $type->getTranslationDomain()),
            'link'     => '#',
            'icon'     => $type->getParameter(ParametersEnum::ICON, 'fas fa-circle'),
            'priority' => 3500,
        ]);

        $registry->add($root . '_item', [
            'label'    => $this->helper->trans('node', [], $type->getTranslationDomain()),
            'link'     => $this->helper->generateUrl('backend.node', [ 'node_type' => $type->getType() ]),
            'parent'   => $root,
        ]);

        foreach ($type->getTaxonomies() as $tax) {
            $taxonomy = $this->taxonomyRegistry->getType($tax['taxonomy']);

            $registry->add($root . '_' . $taxonomy->getType(), [
                'label'    => $this->helper->trans('taxonomy', [], $taxonomy->getTranslationDomain()),
                'link'     => $this->helper->generateUrl('backend.term', [ 'taxonomyType' => $taxonomy->getType() ]),
                'parent'   => $root,
            ]);
        }
    }
}
