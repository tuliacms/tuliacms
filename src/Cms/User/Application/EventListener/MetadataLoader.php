<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\User\Query\Event\QueryFilterEvent;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataLoader implements EventSubscriberInterface
{
    protected Loader $loader;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            QueryFilterEvent::class => ['handle', 100],
        ];
    }

    public function handle(QueryFilterEvent $event): void
    {
        foreach ($event->getCollection() as $user) {
            $this->loader->load($user);
        }
    }
}
