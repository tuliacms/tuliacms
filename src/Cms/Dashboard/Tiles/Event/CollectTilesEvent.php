<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Tiles\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Dashboard\Tiles\TileInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CollectTilesEvent extends Event
{
    protected array $tiles = [];
    protected string $group;

    public function __construct(string $group)
    {
        $this->group = $group;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function add(TileInterface $tile): void
    {
        $this->tiles[] = $tile;
    }

    public function getAll(): array
    {
        usort($this->tiles, function ($a, $b) {
            return $a->getPriority() - $b->getPriority();
        });

        return $this->tiles;
    }
}
