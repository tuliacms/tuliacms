<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMetadataEnum
{
    public const TYPE = 'page';

    public const CATEGORY_ID = 'category_id';
    public const CONTENT = 'content';
    public const TAGS_IDS = 'tags_ids';
    public const THUMBNAIL = 'thumbnail';
}
