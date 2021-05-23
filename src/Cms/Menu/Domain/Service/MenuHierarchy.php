<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Service;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class MenuHierarchy
{
    public function updateHierarchy(Menu $menu, array $hierarchy): void
    {
        $rebuildedHierarchy = [];

        foreach ($hierarchy as $child => $parent) {
            $rebuildedHierarchy[$parent][] = $child;
        }

        foreach ($rebuildedHierarchy as $parent => $items) {
            foreach ($items as $level => $id) {
                $item = $menu->getItem($id);
                $item->setParentId($parent ?: Item::ROOT_ID);
                $item->setPosition($level + 1);
            }
        }

        $this->calculateLevel($menu, Item::ROOT_ID, Item::ROOT_LEVEL);
    }

    public function calculateLevel(Menu $menu, string $parentId, int $baseLevel): void
    {
        foreach ($menu->items() as $item) {
            if ($item->getParentId() === $parentId) {
                $item->setLevel($baseLevel + 1);

                $this->calculateLevel($menu, $item->getId(), $baseLevel + 1);
            }
        }
    }
}
