<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel;

use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuRepositoryInterface
{
    public function createNewMenu(array $data = []): Menu;

    public function createNewItem(array $data = []): Item;

    /**
     * @throws MenuNotFoundException
     */
    public function find(string $id): Menu;

    public function save(Menu $menu): void;

    public function update(Menu $menu): void;

    public function delete(string $id): void;
}
