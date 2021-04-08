<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder\Factory;

use Tulia\Cms\Menu\Application\Query\Finder\Model\Item;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuFactoryInterface
{
    /**
     * @param array $data
     *
     * @return Menu
     */
    public function createNewMenu(array $data = []): Menu;

    /**
     * @param array $data
     *
     * @return Item
     */
    public function createNewItem(array $data = []): Item;
}
