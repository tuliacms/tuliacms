<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Form\Extension;

use Tulia\Component\FormBuilder\ExtensionAggregateInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeExtensionAggregate implements ExtensionAggregateInterface
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param RegistryInterface $registry
     */
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
