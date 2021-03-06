<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Domain\Tiles;

use Tulia\Cms\Dashboard\Ports\Domain\Tiles\DashboardTilesCollector;
use Tulia\Cms\Dashboard\Ports\Domain\Tiles\DashboardTilesRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DashboardTilesRegistry implements DashboardTilesRegistryInterface
{
    /**
     * @var DashboardTilesCollector[]
     */
    private iterable $collectors;

    public function __construct(iterable $collectors)
    {
        $this->collectors = $collectors;
    }

    /**
     * {@inheritdoc}
     */
    public function getTiles(string $group): array
    {
        $collection = new DashboardTilesCollection();

        foreach ($this->collectors as $collector) {
            if ($collector->supports($group)) {
                $collector->collect($collection);
            }
        }

        return $collection->getAll();
    }
}
