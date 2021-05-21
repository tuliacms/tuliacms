<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\MenuActionInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class LevelCalculator implements MenuActionInterface
{
    public static function supports(): array
    {
        return ['save' => 500, 'update' => 500];
    }

    public function execute(Menu $menu): void
    {
        foreach ($menu->items() as $item) {
            if ($item->isRoot()) {
                $newLevel = 0;
            } else {
                $parent = $menu->getItem($item->getParentId());
                $newLevel = $parent->getLevel() + 1;
            }

            if ($item->getLevel() !== $newLevel) {
                $item->setLevel($newLevel);
            }
        }
    }
}
