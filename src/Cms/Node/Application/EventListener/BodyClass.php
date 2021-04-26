<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClass implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CollectBodyClassEvent::class => ['handle', 0],
        ];
    }

    public function handle(CollectBodyClassEvent $event): void
    {
        $node = $event->getRequest()->attributes->get('node');

        if (! $node instanceof Node) {
            return;
        }

        $event->add('node-page');
        $event->add('node-type-' . $node->getType());
        $event->add('node-' . $node->getSlug());
        $event->add('node-' . $node->getId());
    }
}
