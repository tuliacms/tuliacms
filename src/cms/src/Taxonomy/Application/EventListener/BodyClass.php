<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\EventListener;

use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClass
{
    /**
     * @param CollectBodyClassEvent $event
     */
    public function handle(CollectBodyClassEvent $event): void
    {
        $term = $event->getRequest()->attributes->get('term');

        if (! $term instanceof Term) {
            return;
        }

        $event->add('term-page');
        $event->add('term-type-' . $term->getType());
        $event->add('term-' . $term->getSlug());
        $event->add('term-' . $term->getId());
    }
}
