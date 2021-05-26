<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Menu;

use Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface as TaxonomyRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyMenuItemTypeRegistrator implements RegistratorInterface
{
    protected TaxonomyRegistryInterface $taxonomyTypeRegistry;

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
            //$type->setSelectorService(Selector::class);
        }
    }
}
