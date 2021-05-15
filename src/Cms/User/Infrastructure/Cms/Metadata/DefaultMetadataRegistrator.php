<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Cms\Metadata;

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
        $fields = $registry->getContentFields(UserMetadataEnum::TYPE);

        $fields->add([
            'name' => UserMetadataEnum::NAME,
            'multilingual' => false,
        ]);

        $fields->add([
            'name' => UserMetadataEnum::AVATAR,
            'multilingual' => false,
        ]);
    }
}
