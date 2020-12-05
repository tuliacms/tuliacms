<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy;

use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\Item as BuilderItem;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\Identity;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\RegistryInterface;
use Tulia\Cms\Menu\Application\Query\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\MultipleFetchException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryNotFetchedException;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Item;
use Tulia\Cms\Menu\Application\Query\Finder\Model\ItemCollection;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HierarchyBuilder implements HierarchyBuilderInterface
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param RegistryInterface $registry
     */
    public function __construct(FinderFactoryInterface $finderFactory, RegistryInterface $registry)
    {
        $this->finderFactory = $finderFactory;
        $this->registry      = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function build(string $id, ItemCollection $collection = null): HierarchyInterface
    {
        $hierarchy = new Hierarchy($id);

        $items = $collection ?? $this->getItems($id);

        foreach ($items as $item) {
            if ($item->getLevel() === 0) {
                $hierarchy->append($this->buildFor($item, $items));
            }
        }

        return $hierarchy;
    }

    /**
     * @param string $id
     *
     * @return ItemCollection
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getItems(string $id): ItemCollection
    {
        $menu = $this->finderFactory->getInstance(ScopeEnum::BUILD_MENU)->find($id);

        if ($menu) {
            return $menu->getItems();
        }

        return new ItemCollection();
    }

    /**
     * @param Item $sourceItem
     * @param ItemCollection $collection
     *
     * @return BuilderItem
     */
    private function buildFor(Item $sourceItem, ItemCollection $collection): BuilderItem
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
