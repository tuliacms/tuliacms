<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Metadata\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMetadataEnum
{
    public const TYPE = 'node';

    public const CATEGORY_ID = 'category_id';
    public const CONTENT = 'content';
    public const TAGS_IDS = 'tags_ids';
    public const THUMBNAIL = 'thumbnail';
}
