<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Platform\Domain\WriteModel\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    private string $menuId;

    public function __construct(string $menuId)
    {
        $this->menuId = $menuId;
    }

    public function getMenuId(): string
    {
        return $this->menuId;
    }
}
