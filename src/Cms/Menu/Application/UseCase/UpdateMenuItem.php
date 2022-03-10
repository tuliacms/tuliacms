<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMenuItem extends AbstractMenuUseCase
{
    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(Menu $menu, Item $item, array $attributes): void
    {
        $data = $this->flattenAttributes($attributes);
        $attributes = $this->removeMenuItemAttributes($attributes);

        $item->setName($data['name']);
        $item->setType($data['type']);
        $item->setVisibility($data['visibility'] ? true : false);
        $item->setIdentity($data['identity']);
        $item->setHash($data['hash']);
        $item->setTarget($data['target']);
        $item->updateAttributes($attributes);

        $this->update($menu);
    }
}
