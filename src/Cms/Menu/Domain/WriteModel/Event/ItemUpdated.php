<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class ItemUpdated extends DomainEvent
{
    private string $itemId;

    public function __construct(string $menuId, string $itemId)
    {
        parent::__construct($menuId);

        $this->itemId = $itemId;
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }
}
