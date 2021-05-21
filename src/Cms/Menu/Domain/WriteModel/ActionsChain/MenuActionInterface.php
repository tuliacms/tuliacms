<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuActionInterface
{
    public static function supports(): array;

    public function execute(Menu $menu): void;
}
