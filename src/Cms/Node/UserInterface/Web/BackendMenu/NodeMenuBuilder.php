<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\BackendMenu;

use Tulia\Cms\BackendMenu\Application\BuilderInterface;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Application\Registry\ItemRegistryInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\Enum\ParametersEnum;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface as NodeRegistry;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface as TaxonomyRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuBuilder implements BuilderInterface
{
    /**
     * @var BuilderHelperInterface
     */
    protected $helper;

    /**
     * @var NodeRegistry
     */
    protected $nodeRegistry;

    /**
     * @var TaxonomyRegistry
     */
    protected $taxonomyRegistry;

    /**
     * @param BuilderHelperInterface $helper
     * @param NodeRegistry $nodeRegistry
     * @param TaxonomyRegistry $taxonomyRegistry
     */
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
                'link'     => $this->helper->generateUrl('backend.term', [ 'taxonomy_type' => $taxonomy->getType() ]),
                'parent'   => $root,
            ]);
        }
    }
}
