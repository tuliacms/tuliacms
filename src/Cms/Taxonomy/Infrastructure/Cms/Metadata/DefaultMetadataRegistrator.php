<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata;

use Tulia\Cms\Metadata\Domain\Registry\RegistratorInterface;
use Tulia\Cms\Metadata\Domain\Registry\ContentFieldsRegistryInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata\TermMetadataEnum;

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
        $fields = $registry->getContentFields('term');

        $fields->add([
            'name' => TermMetadataEnum::THUMBNAIL,
            'multilingual' => false,
        ]);
    }
}
