<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Menu\Application\Model\Item;

/**
 * @author Adam Banaszkiewicz
 */
class ItemEvent extends Event
{
    /**
     * @var Item
     */
    protected $item;

    /**
     * @param Item $item
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }
}
