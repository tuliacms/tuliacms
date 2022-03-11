<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class CreateMenuItem extends AbstractMenuUseCase
{
    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(Menu $menu, array $attributes): void
    {
        $item = $this->repository->createNewItem($menu);

        $menu->updateItemUsingAttributes(
            $item->getId(),
            $this->flattenAttributes($attributes),
            $this->removeMenuItemAttributes($attributes)
        );

        $this->update($menu);
    }
}
