<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Menu\Event;

use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\ItemId;

/**
 * @author Adam Banaszkiewicz
 */
class ItemAdded extends DomainEvent
{
    /**
     * @var ItemId
     */
    private $itemId;

    /**
     * @param AggregateId $menuId
     * @param ItemId $itemId
     */
    public function __construct(AggregateId $menuId, ItemId $itemId)
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
