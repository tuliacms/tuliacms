<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\Extension;

use Tulia\Component\FormSkeleton\Extension\ExtensionAggregateInterface;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeExtensionAggregate implements ExtensionAggregateInterface
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
            $extensions[] = new TaxonomyTypeExtension($type);
        }

        return $extensions;
    }
}
