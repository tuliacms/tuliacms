<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata;

use Tulia\Cms\Metadata\Registrator\RegistratorInterface;
use Tulia\Cms\Metadata\Registrator\RegistryInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata\TermMetadataEnum;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultMetadataRegistrator implements RegistratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        $fields = $registry->getContentFields('term');

        $fields->add([
            'name' => TermMetadataEnum::THUMBNAIL,
            'multilingual' => false,
        ]);
    }
}
