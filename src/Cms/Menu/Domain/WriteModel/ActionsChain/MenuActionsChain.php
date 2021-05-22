<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class MenuActionsChain implements MenuActionsChainInterface
{
    protected array $actions = [];

    public function addAction(MenuActionInterface $action, string $name, int $priority): void
    {
        $this->actions[$name][$priority][] = $action;
    }

    public function execute(string $name, Menu $menu): void
    {
        if (isset($this->actions[$name]) === false) {
            return;
        }

        krsort($this->actions[$name]);

        foreach ($this->actions[$name] as $actions) {
            foreach ($actions as $action) {
                $action->execute($menu);
            }
        }
    }
}
