<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Bus\EventBus\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Event\ContentTypeCreated;
use Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider\CachedContentTypeRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class CacheClearerSubscriber implements EventSubscriberInterface
{
    private CachedContentTypeRegistry $registry;

    public function __construct(CachedContentTypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentTypeCreated::class => 'clearCache',
        ];
    }

    public function clearCache(ContentTypeCreated $event): void
    {
        $this->registry->clearCache();
    }
}
