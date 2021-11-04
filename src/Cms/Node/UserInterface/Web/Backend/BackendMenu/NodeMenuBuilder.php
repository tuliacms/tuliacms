<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\BackendMenu;

use Tulia\Cms\BackendMenu\Ports\Domain\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Registry\ItemRegistryInterface;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface as TaxonomyRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuBuilder implements BuilderInterface
{
    protected BuilderHelperInterface $helper;

    protected NodeTypeRegistry $nodeRegistry;

    protected TaxonomyRegistry $taxonomyRegistry;

    public function __construct(
        BuilderHelperInterface $helper,
        NodeTypeRegistry $nodeRegistry,
        TaxonomyRegistry $taxonomyRegistry
    ) {
        $this->helper = $helper;
        $this->nodeRegistry = $nodeRegistry;
        $this->taxonomyRegistry = $taxonomyRegistry;
    }

    public function build(ItemRegistryInterface $registry): void
    {
        foreach ($this->nodeRegistry->getTypes() as $type) {
            $this->registerNodeType($registry, $this->nodeRegistry->get($type));
        }
    }

    private function registerNodeType(ItemRegistryInterface $registry, NodeType $type): void
    {
        $root = 'node_' . $type->getType();

        $registry->add($root, [
            'label'    => $this->helper->trans('node', [], $type->getTranslationDomain()),
            'link'     => '#',
            'icon'     => $type->getIcon(),
            'priority' => 3500,
        ]);

        $registry->add($root . '_item', [
            'label'    => $this->helper->trans('node', [], $type->getTranslationDomain()),
            'link'     => $this->helper->generateUrl('backend.node', [ 'node_type' => $type->getType() ]),
            'parent'   => $root,
        ]);

        /*foreach ($type->getTaxonomies() as $tax) {
            $taxonomy = $this->taxonomyRegistry->getType($tax['taxonomy']);

            $registry->add($root . '_' . $taxonomy->getType(), [
                'label'    => $this->helper->trans('taxonomy', [], $taxonomy->getTranslationDomain()),
                'link'     => $this->helper->generateUrl('backend.term', [ 'taxonomyType' => $taxonomy->getType() ]),
                'parent'   => $root,
            ]);
        }*/
    }
}
