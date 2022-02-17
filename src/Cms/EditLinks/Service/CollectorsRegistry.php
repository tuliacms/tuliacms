<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\Service;

/**
 * @author Adam Banaszkiewicz
 */
class CollectorsRegistry
{
    /**
     * @var EditLinksCollectorInterface[]
     */
    private iterable $collectors;

    public function __construct(iterable $collectors)
    {
        $this->collectors = $collectors;
    }

    /**
     * @return EditLinksCollectorInterface[]
     */
    public function getSupported(object $object): array
    {
        $collectors = [];

        foreach ($this->collectors as $collector) {
            if ($collector->supports($object)) {
                $collectors[] = $collector;
            }
        }

        return $collectors;
    }
}
