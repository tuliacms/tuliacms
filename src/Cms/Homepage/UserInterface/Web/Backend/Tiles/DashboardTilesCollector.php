<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles;

/**
 * @author Adam Banaszkiewicz
 */
interface DashboardTilesCollector
{
    public function collect(DashboardTilesCollection $collection): void;

    public function supports(string $group): bool;
}
