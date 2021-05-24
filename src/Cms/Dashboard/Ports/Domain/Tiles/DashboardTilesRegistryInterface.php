<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Ports\Domain\Tiles;

/**
 * @author Adam Banaszkiewicz
 */
interface DashboardTilesRegistryInterface
{
    public function getTiles(string $group): array;
}
