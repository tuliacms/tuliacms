<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\NodeType;

use Tulia\Cms\Node\Infrastructure\NodeType\Enum\ParametersEnum;

/**
 * Registers default CMS node (page) in system.
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
        $type = $registry->registerType('page');
        $type->addSupport([ 'introduction', 'thumbnail', 'quick-create', 'searchable', 'hierarchy' ]);
        $type->addTaxonomy('category');
        $type->addTaxonomy('tag');
        $type->setRoutableTaxonomy('category');
        $type->setTranslationDomain('pages');
        $type->setParameter(ParametersEnum::ICON, 'fas fa-file-powerpoint');
    }
}
