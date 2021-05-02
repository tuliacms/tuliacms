<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\MenuId;
use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    /**
     * @var MenuId
     */
    private $menuId;

    /**
     * @param MenuId $menuId
     */
    public function __construct(MenuId $menuId)
    {
        $this->menuId = $menuId;
    }

    /**
     * @return MenuId
     */
    public function getMenuId(): MenuId
    {
        return $this->menuId;
    }
}
