<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Event\DomainEvent;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuCreated;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuDeleted;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuUpdated;

/**
 * @author Adam Banaszkiewicz
 */
class MenuCacheClearer implements EventSubscriberInterface
{
    private TagAwareCacheInterface $menuCache;

    public function __construct(TagAwareCacheInterface $menuCache)
    {
        $this->menuCache = $menuCache;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuCreated::class => 'clearMenuCache',
            MenuDeleted::class => 'clearMenuCache',
            MenuUpdated::class => 'clearMenuCache',
        ];
    }

    public function clearMenuCache(DomainEvent $event): void
    {
        $this->menuCache->invalidateTags([sprintf('menu_%s', $event->getMenuId())]);
    }
}
