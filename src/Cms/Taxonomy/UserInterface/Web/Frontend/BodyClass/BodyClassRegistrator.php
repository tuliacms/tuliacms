<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\BodyClass;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\BodyClass\Domain\BodyClassCollection;
use Tulia\Cms\BodyClass\Ports\Domain\BodyClassCollectorInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClassRegistrator implements BodyClassCollectorInterface
{
    public function collect(Request $request, BodyClassCollection $collection): void
    {
        $term = $request->attributes->get('term');

        if (! $term instanceof Term) {
            return;
        }

        $collection->add('is-term-page');
        $collection->add('is-term-type-' . $term->getType());
        $collection->add('is-term-slug-' . $term->getSlug());
        $collection->add('is-term-' . $term->getId());
    }
}
