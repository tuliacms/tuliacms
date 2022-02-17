<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\Service;

use Tulia\Cms\EditLinks\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinksCollector
{
    private CollectorsRegistry $registry;

    public function __construct(CollectorsRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function collect(object $object, array $options = []): array
    {
        $collection = new Collection();
        $collectors = $this->registry->getSupported($object);

        foreach ($collectors as $collector) {
            $collector->collect($collection, $object, $options);
        }

        return $collection->getAll();
    }
}
