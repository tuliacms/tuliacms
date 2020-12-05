<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Tiles;

/**
 * @author Adam Banaszkiewicz
 */
interface ManagerInterface
{
    /**
     * @param string $group
     *
     * @return array
     */
    public function getTiles(string $group): array;
}
