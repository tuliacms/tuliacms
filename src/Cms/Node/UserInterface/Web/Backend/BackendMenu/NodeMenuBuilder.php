<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\BackendMenu;

use Tulia\Cms\BackendMenu\Ports\Domain\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Registry\ItemRegistryInterface;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\TaxonomyTypeRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuBuilder implements BuilderInterface
{
    protected BuilderHelperInterface $helper;

    protected NodeTypeRegistry $nodeRegistry;

    protected TaxonomyTypeRegistry $taxonomyTypeRegistry;

    public function __construct(
        BuilderHelperInterface $helper,
        NodeTypeRegistry $nodeRegistry,
        TaxonomyTypeRegistry $taxonomyTypeRegistry
    ) {
        $this->helper = $helper;
        $this->nodeRegistry = $nodeRegistry;
        $this->taxonomyTypeRegistry = $taxonomyTypeRegistry;
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
            'label'    => $this->helper->trans($type->getName(), [], 'node'),
            'link'     => '#',
            'icon'     => $type->getIcon(),
            'priority' => 3500,
        ]);

        $registry->add($root . '_item', [
            'label'  => $this->helper->trans('nodesListOfType', ['type' => $this->helper->trans($type->getName(), [], 'node')], 'node'),
            'link'   => $this->helper->generateUrl('backend.node', [ 'node_type' => $type->getType() ]),
            'parent' => $root,
        ]);

        foreach ($type->getFields() as $field) {
            if ($field->getType() !== 'taxonomy') {
                continue;
            }

            $taxonomy = $this->taxonomyTypeRegistry->get($field->getTaxonomy());

            $registry->add($root . '_' . $taxonomy->getType(), [
                'label'  => $this->helper->trans('termsListOfTaxonomy', ['taxonomy' => $taxonomy->getType()], 'taxonomy'),
                'link'   => $this->helper->generateUrl('backend.term', [ 'taxonomyType' => $taxonomy->getType() ]),
                'parent' => $root,
            ]);
        }
    }
}
