<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Collector;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClassService
{
    /**
     * @var BodyClassCollectorInterface[]
     */
    private iterable $collectors;

    public function __construct(iterable $collectors)
    {
        $this->collectors = $collectors;
    }

    public function collect(Request $request): BodyClassCollection
    {
        $collection = new BodyClassCollection();

        foreach ($this->collectors as $collector) {
            $collector->collect($request, $collection);
        }

        return $collection;
    }
}
