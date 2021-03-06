<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\TaxonomyType;

/**
 * Registers default CMS taxonomies (categories and tags) in system.
 *
 * @author Adam Banaszkiewicz
 */
class DefaultTypesRegistrator implements RegistratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        $type = $registry->registerType('category');
        $type->addSupport([ 'thumbnail', 'quick-create', 'hierarchy' ]);
        $type->setTranslationDomain('categories');
        $type->setRoutingStrategy('full_path');

        $type = $registry->registerType('tag');
        $type->addSupport([ 'quick-create' ]);
        $type->setTranslationDomain('tags');
    }
}
