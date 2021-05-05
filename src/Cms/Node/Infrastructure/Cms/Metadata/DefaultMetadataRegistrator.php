<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Metadata;

use Tulia\Cms\Metadata\Registrator\RegistratorInterface;
use Tulia\Cms\Metadata\Registrator\RegistryInterface;
use Tulia\Cms\Node\Infrastructure\Cms\Metadata\Enum\MetadataEnum;

/**
 * Registers default CMS node (page) metadatas in system.
 *
 * @author Adam Banaszkiewicz
 */
class DefaultMetadataRegistrator implements RegistratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        $fields = $registry->getContentFields('node');

        /**
         * Stores content of node with parsed data. Stands like a cache for Node's content,
         * after parsing Shortcodes and other elements.
         */
        $fields->add([
            'name' => MetadataEnum::CONTENT,
            'multilingual' => true,
        ]);

        /**
         * Node's thumbnail filepath.
         */
        $fields->add([
            'name' => MetadataEnum::THUMBNAIL,
            'multilingual' => false,
        ]);

        /**
         * Node's main category ID.
         */
        $fields->add([
            'name' => MetadataEnum::CATEGORY_ID,
            'multilingual' => false,
        ]);

        /**
         * Node's tags' ID.
         */
        $fields->add([
            'name' => MetadataEnum::TAGS_IDS,
            'datatype' => 'array',
            'multilingual' => false,
        ]);

        $fields = $registry->getContentFields('menu_item');
        $fields->add([
            'name' => 'menu_metadata',
            'datatype' => 'string',
            'multilingual' => false,
        ]);
    }
}
