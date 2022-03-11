<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuRepositoryInterface
{
    public function createNewMenu(): Menu;

    public function createNewItem(Menu $menu): Item;

    public function find(string $id): ?Menu;

    public function save(Menu $menu): void;

    public function delete(Menu $menu): void;
}
