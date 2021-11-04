<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Menu;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Menu\Selector;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuItemTypeRegistrator implements RegistratorInterface
{
    private NodeTypeRegistry $nodeTypeRegistry;

    private Selector $selector;

    public function __construct(NodeTypeRegistry $nodeTypeRegistry, Selector $selector)
    {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->selector = $selector;
    }

    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        foreach ($this->nodeTypeRegistry->all() as $nodeType) {
            $type = $registry->registerType('node:' . $nodeType->getType());
            $type->setLabel('node');
            $type->setTranslationDomain($nodeType->getTranslationDomain());
            $type->setSelectorService($this->selector);
        }
    }
}
