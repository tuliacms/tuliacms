<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\MenuActionInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class ParentRootResolver implements MenuActionInterface
{
    public static function supports(): array
    {
        return ['save' => 900, 'update' => 900];
    }

    public function execute(Menu $menu): void
    {
        foreach ($menu->items() as $item) {
            if ($item->getParentId() === null && $item->isRoot() === false) {
                $item->setParentId(Item::ROOT_ID);
            }
        }
    }
}
