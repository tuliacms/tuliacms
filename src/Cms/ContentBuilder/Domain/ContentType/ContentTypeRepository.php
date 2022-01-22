<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeRepository
{
    private ContentTypeStorageInterface $contentTypeStorage;

    /*public function __construct(ContentTypeStorageInterface $contentTypeStorage)
    {
        $this->contentTypeStorage = $contentTypeStorage;
    }*/

    public function insert(ContentType $contentType): void
    {

    }

    public function update(ContentType $contentType): void
    {

    }

    public function delete(ContentType $contentType): void
    {

    }
}
