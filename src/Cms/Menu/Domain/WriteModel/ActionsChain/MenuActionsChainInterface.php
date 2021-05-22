<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuActionsChainInterface
{
    public function execute(string $name, Menu $menu): void;

    public function addAction(MenuActionInterface $action, string $name, int $priority): void;
}
