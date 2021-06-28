<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Frontend\BodyClass;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\BodyClass\Domain\BodyClassCollection;
use Tulia\Cms\BodyClass\Ports\Domain\BodyClassCollectorInterface;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClassRegistrator implements BodyClassCollectorInterface
{
    public function collect(Request $request, BodyClassCollection $collection): void
    {
        $node = $request->attributes->get('node');

        if (! $node instanceof Node) {
            return;
        }

        $collection->add('is-node-page');
        $collection->add('is-node-type-' . $node->getType());
        $collection->add('is-node-slug-' . $node->getSlug());
        $collection->add('is-node-' . $node->getId());
    }
}
