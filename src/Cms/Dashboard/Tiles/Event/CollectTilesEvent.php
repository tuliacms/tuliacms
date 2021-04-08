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
    /**
     * @var array
     */
    protected $tiles = [];

    /**
     * @var string
     */
    protected $group;

    /**
     * @param string $group
     */
    public function __construct(string $group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param TileInterface $tile
     */
    public function add(TileInterface $tile): void
    {
        $this->tiles[] = $tile;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        usort($this->tiles, function ($a, $b) {
            return $a->getPriority() - $b->getPriority();
        });

        return $this->tiles;
    }
}
