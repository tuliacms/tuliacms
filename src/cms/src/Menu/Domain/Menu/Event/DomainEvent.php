<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Menu\Event;

use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    /**
     * @var AggregateId
     */
    private $menuId;

    /**
     * @param AggregateId $menuId
     */
    public function __construct(AggregateId $menuId)
    {
        $this->menuId = $menuId;
    }

    /**
     * @return AggregateId
     */
    public function getMenuId(): AggregateId
    {
        return $this->menuId;
    }
}
