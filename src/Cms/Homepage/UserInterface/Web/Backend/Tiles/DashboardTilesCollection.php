<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles;

/**
 * @author Adam Banaszkiewicz
 */
class DashboardTilesCollection
{
    protected array $tiles = [];

    public function add(string $name, array $tile): self
    {
        $this->tiles[$name] = array_merge([
            'priority' => 0,
            'description' => null,
            'name' => null,
            'link' => null,
            'icon' => null,
        ], $tile);

        return $this;
    }

    public function getAll(): array
    {
        usort($this->tiles, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        return $this->tiles;
    }
}
