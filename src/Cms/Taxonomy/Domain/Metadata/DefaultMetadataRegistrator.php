<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Metadata;

use Tulia\Cms\Metadata\Domain\Registry\RegistratorInterface;
use Tulia\Cms\Metadata\Domain\Registry\ContentFieldsRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultMetadataRegistrator implements RegistratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContentFieldsRegistryInterface $registry): void
    {
        $fields = $registry->getContentFields(TermMetadataEnum::TYPE);

        $fields->add([
            'name' => TermMetadataEnum::THUMBNAIL,
            'multilingual' => false,
        ]);
    }
}
