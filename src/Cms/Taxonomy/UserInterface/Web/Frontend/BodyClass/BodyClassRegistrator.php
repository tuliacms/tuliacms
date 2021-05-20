<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\BodyClass;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Model\Term;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;

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
        $term = $event->getRequest()->attributes->get('term');

        if (! $term instanceof Term) {
            return;
        }

        $event->add('term-page');
        $event->add('term-type-' . $term->getType());
        $event->add('term-slug-' . $term->getSlug());
        $event->add('term-' . $term->getId());
    }
}
