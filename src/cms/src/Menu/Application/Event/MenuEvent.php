<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Menu\Application\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class MenuEvent extends Event
{
    /**
     * @var Menu
     */
    protected $menu;

    /**
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return Menu
     */
    public function getMenu(): Menu
    {
        return $this->menu;
    }
}
