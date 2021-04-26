<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Node\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\Node\Query\Event\QueryFilterEvent;

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
            QueryFilterEvent::class => ['handle', 0],
        ];
    }

    public function handle(QueryFilterEvent $event): void
    {
        foreach ($event->getCollection() as $node) {
            $this->loader->load($node);
        }
    }
}
