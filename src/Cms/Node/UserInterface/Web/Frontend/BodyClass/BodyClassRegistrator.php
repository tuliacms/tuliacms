<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Frontend\BodyClass;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClassRegistrator implements EventSubscriberInterface
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
        $event->add('node-slug-' . $node->getSlug());
        $event->add('node-' . $node->getId());
    }
}
