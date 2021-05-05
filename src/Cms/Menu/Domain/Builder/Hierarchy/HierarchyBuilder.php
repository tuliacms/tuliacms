<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\Item as BuilderItem;
use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\RegistryInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\Model\Item;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\ReadModel\MenuFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HierarchyBuilder implements HierarchyBuilderInterface
{
    protected MenuFinderInterface $menuFinder;
    protected RegistryInterface $registry;

    public function __construct(MenuFinderInterface $menuFinder, RegistryInterface $registry)
    {
        $this->menuFinder = $menuFinder;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function build(string $id, array $collection = []): HierarchyInterface
    {
        $hierarchy = new Hierarchy($id);

        $items = $collection === [] ? $this->getItems($id) : $collection;

        foreach ($items as $item) {
            if ($item->getLevel() === 0) {
                $hierarchy->append($this->buildFor($item, $items));
            }
        }

        return $hierarchy;
    }

    private function getItems(string $id): array
    {
        $menu = $this->menuFinder->findOne(['id' => $id], ScopeEnum::BUILD_MENU);

        return $menu ? $menu->getItems() : [];
    }

    private function buildFor(Item $sourceItem, array $collection): BuilderItem
    {
        $identity = $this->registry->provide($sourceItem->getType(), $sourceItem->getIdentity());

        $item = new BuilderItem();
        $item->setId($sourceItem->getId());
        $item->setLevel($sourceItem->getLevel());
        $item->setLabel($sourceItem->getName());
        $item->setTarget($sourceItem->getTarget());
        $item->setHash($sourceItem->getHash());
        $item->setIdentity($identity ?: new Identity(''));

        /** @var Item $cItem */
        foreach ($collection as $cItem) {
            if ($cItem->getParentId() === $sourceItem->getId()) {
                $item->addChild($this->buildFor($cItem, $collection));
            }
        }

        return $item;
    }
}
