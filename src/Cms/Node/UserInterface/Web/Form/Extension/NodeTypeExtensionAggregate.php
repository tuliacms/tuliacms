<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form\Extension;

use Tulia\Component\FormBuilder\Extension\ExtensionAggregateInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeExtensionAggregate implements ExtensionAggregateInterface
{
    protected RegistryInterface $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function aggregate(): array
    {
        $extensions = [];

        foreach ($this->registry->all() as $type) {
            $extensions[] = new NodeTypeExtension($type);
        }

        return $extensions;
    }
}
