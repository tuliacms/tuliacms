<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Form\Extension;

use Tulia\Component\FormSkeleton\Extension\ExtensionAggregateInterface;
use Tulia\Cms\Node\Domain\NodeType\NodeTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeExtensionAggregate implements ExtensionAggregateInterface
{
    protected NodeTypeRegistryInterface $registry;

    public function __construct(NodeTypeRegistryInterface $registry)
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
