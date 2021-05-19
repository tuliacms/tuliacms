<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Metadata;

use Tulia\Cms\Metadata\Domain\Registry\RegistratorInterface;
use Tulia\Cms\Metadata\Domain\Registry\ContentFieldsRegistryInterface;

/**
 * Registers default CMS node (page) metadatas in system.
 * @author Adam Banaszkiewicz
 */
class DefaultMetadataRegistrator implements RegistratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContentFieldsRegistryInterface $registry): void
    {
        $fields = $registry->getContentFields(NodeMetadataEnum::TYPE);

        /**
         * Stores content of node with parsed data. Stands like a cache for Node's content,
         * after parsing Shortcodes and other elements.
         */
        $fields->add([
            'name' => NodeMetadataEnum::CONTENT,
            'multilingual' => true,
        ]);

        /**
         * Node's thumbnail filepath.
         */
        $fields->add([
            'name' => NodeMetadataEnum::THUMBNAIL,
            'multilingual' => false,
        ]);

        /**
         * Node's main category ID.
         */
        $fields->add([
            'name' => NodeMetadataEnum::CATEGORY_ID,
            'multilingual' => false,
        ]);

        /**
         * Node's tags' ID.
         */
        $fields->add([
            'name' => NodeMetadataEnum::TAGS_IDS,
            'datatype' => 'array',
            'multilingual' => false,
        ]);
    }
}
