<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu;

use Tulia\Cms\Menu\Infrastructure\Builder\Type\RegistratorInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\RegistryInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface as TaxonomyRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TypeRegistrator implements RegistratorInterface
{
    /**
     * @var TaxonomyRegistryInterface
     */
    protected $taxonomyTypeRegistry;

    /**
     * @param TaxonomyRegistryInterface $taxonomyTypeRegistry
     */
    public function __construct(TaxonomyRegistryInterface $taxonomyTypeRegistry)
    {
        $this->taxonomyTypeRegistry = $taxonomyTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        /** @var TaxonomyTypeInterface $taxonomyType */
        foreach ($this->taxonomyTypeRegistry->all() as $taxonomyType) {
            $type = $registry->registerType('term:' . $taxonomyType->getType());
            $type->setLabel('taxonomy');
            $type->setTranslationDomain($taxonomyType->getTranslationDomain());
            $type->setSelectorService(Selector::class);
        }
    }
}
