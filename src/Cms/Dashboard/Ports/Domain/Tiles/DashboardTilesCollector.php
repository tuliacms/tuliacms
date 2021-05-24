<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Ports\Domain\Tiles;

use Tulia\Cms\Dashboard\Domain\Tiles\DashboardTilesCollection;

/**
 * @author Adam Banaszkiewicz
 */
interface DashboardTilesCollector
{
    public function collect(DashboardTilesCollection $collection): void;

    public function supports(string $group): bool;
}
