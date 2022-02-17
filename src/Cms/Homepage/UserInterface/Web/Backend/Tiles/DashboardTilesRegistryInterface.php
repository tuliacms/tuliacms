<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles;

/**
 * @author Adam Banaszkiewicz
 */
interface DashboardTilesRegistryInterface
{
    public function getTiles(string $group): array;
}
