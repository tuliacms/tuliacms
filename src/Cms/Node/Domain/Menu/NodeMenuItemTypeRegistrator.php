<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Menu;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Menu\Selector;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuItemTypeRegistrator implements RegistratorInterface
{
    private ContentTypeRegistryInterface $contentTypeRegistry;

    private Selector $selector;

    public function __construct(ContentTypeRegistryInterface $contentTypeRegistry, Selector $selector)
    {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->selector = $selector;
    }

    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        foreach ($this->contentTypeRegistry->all() as $nodeType) {
            if ($nodeType->isType('node')) {
                $type = $registry->registerType('node:' . $nodeType->getCode());
                $type->setLabel($nodeType->getName());
                $type->setSelectorService($this->selector);
            }
        }
    }
}
