<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMenu extends AbstractMenuUseCase
{
    public function __invoke(Menu $menu): void
    {
        $this->update($menu);
    }
}
