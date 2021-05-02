<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\MenuId;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\ItemId;

/**
 * @author Adam Banaszkiewicz
 */
class ItemRemoved extends DomainEvent
{
    /**
     * @var ItemId
     */
    private $itemId;

    /**
     * @param MenuId $menuId
     * @param ItemId $itemId
     */
    public function __construct(MenuId $menuId, ItemId $itemId)
    {
        parent::__construct($menuId);

        $this->itemId = $itemId;
    }

    /**
     * @return ItemId
     */
    public function getItemId(): ItemId
    {
        return $this->itemId;
    }
}
