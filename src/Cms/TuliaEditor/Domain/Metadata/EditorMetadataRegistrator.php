<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\Domain\Metadata;

use Tulia\Cms\Metadata\Domain\Registry\RegistratorInterface;
use Tulia\Cms\Metadata\Domain\Registry\ContentFieldsRegistryInterface;
use Tulia\Cms\Node\Domain\Metadata\NodeMetadataEnum;

/**
 * @author Adam Banaszkiewicz
 */
class EditorMetadataRegistrator implements RegistratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContentFieldsRegistryInterface $registry): void
    {
        $fields = $registry->getContentFields(NodeMetadataEnum::TYPE);

        $fields->add([
            'name' => 'tulia-editor-data',
            'multilingual' => true,
        ]);
    }
}
