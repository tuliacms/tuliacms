<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class MenuCreated extends DomainEvent
{
    public static function fromModel(Menu $menu): self
    {
        return new self($menu->getId());
    }
}
