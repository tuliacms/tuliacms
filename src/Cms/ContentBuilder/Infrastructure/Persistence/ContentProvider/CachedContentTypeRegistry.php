<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecorator;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class CachedContentTypeRegistry extends ContentTypeRegistry
{
    private CacheInterface $contentBuilderCache;

    public function __construct(ContentTypeDecorator $decorator, CacheInterface $contentBuilderCache)
    {
        parent::__construct($decorator);

        $this->contentBuilderCache = $contentBuilderCache;
    }

    protected function fetch(): array
    {
        return $this->contentTypes = $this->contentBuilderCache->get('tulia.content_builder.content_types', function (ItemInterface $item) {
            return parent::fetch();
        });
    }

    public function clearCache(): void
    {
        $this->contentBuilderCache->delete('tulia.content_builder.content_types');
    }
}
