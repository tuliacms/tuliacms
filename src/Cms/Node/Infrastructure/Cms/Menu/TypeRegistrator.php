<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Menu;

use Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface as NodeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TypeRegistrator implements RegistratorInterface
{
    /**
     * @var NodeRegistryInterface
     */
    protected $nodeTypeRegistry;

    /**
     * @param NodeRegistryInterface $nodeTypeRegistry
     */
    public function __construct(NodeRegistryInterface $nodeTypeRegistry)
    {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        /** @var NodeTypeInterface $nodeType */
        foreach ($this->nodeTypeRegistry->all() as $nodeType) {
            $type = $registry->registerType('node:' . $nodeType->getType());
            $type->setLabel('node');
            $type->setTranslationDomain($nodeType->getTranslationDomain());
            $type->setSelectorService(Selector::class);
        }
    }
}
