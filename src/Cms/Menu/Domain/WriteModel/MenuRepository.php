<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Menu\Application\Query\Finder\Factory\MenuFactory;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\MenuId;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\ItemId;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\MenuStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuRepository
{
    private MenuStorageInterface $storage;
    private MenuFactory $menuFactory;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        MenuStorageInterface $storage,
        MenuFactory $menuFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->storage = $storage;
        $this->menuFactory = $menuFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createNewMenu(array $data = []): Menu
    {
        return $this->menuFactory->createNewMenu($data);
    }

    public function createNewItem(array $data = []): Item
    {
        return $this->menuFactory->createNewItem($data);
    }

    public function find(MenuId $id): Menu
    {
        $menu = $this->connection->fetchAll('
            SELECT *
            FROM #__menu AS tm
            WHERE tm.id = :id
            LIMIT 1', [
            'id' => $id->getId(),
        ]);

        if (empty($menu)) {
            throw new MenuNotFoundException();
        }

        $menu = reset($menu);
        $menu['items'] = $this->itemDbalRepository->findItems($id);

        return $this->transformer->arrayToAggregate($menu);
    }

    public function save(Menu $menu): void
    {
        $this->storage->insert($this->extract($menu));

        //$this->eventDispatcher->dispatch(new WebsiteCreated($website->getId()->getId()));
        /*$this->connection->transactional(function () use ($menu) {
            if ($this->recordExists($menu->getId()->getId())) {
                $this->update($menu);
            } else {
                $this->insert($menu);
            }

            foreach ($menu->getItemsChanges() as $change) {
                $id = $change['id'];

                if ($change['type'] === 'update') {
                    $this->itemDbalRepository->save($menu->getItem(new ItemId($id)));
                }
                if ($change['type'] === 'add') {
                    $this->itemDbalRepository->save($menu->getItem(new ItemId($id)));
                }
                if ($change['type'] === 'remove') {
                    $this->itemDbalRepository->delete(new ItemId($id));
                }
            }
        });*/
    }

    private function insert(Menu $menu): void
    {
        $this->connection->insert(
            '#__menu',
            $this->transformer->aggregateToInsert($menu)
        );
    }

    private function update(Menu $menu): void
    {
        $this->connection->update(
            '#__menu',
            $this->transformer->aggregateToUpdate($menu),
            ['id' => $menu->getId()->getId()]
        );
    }

    /**
     * @param Menu $menu
     */
    public function delete(Menu $menu): void
    {
        $this->connection->transactional(function () use ($menu) {
            $this->connection->delete('#__menu', ['id' => $menu->getId()->getId()]);
        });
    }

    private function extract(Menu $menu): array
    {
        $data = [];
        $data['id'] = $menu->getId()->getId();
        $data['name'] = $menu->getName();
        $data['website_id'] = $menu->getWebsiteId();
        $data['items'] = [];

        foreach ($menu->getItems() as $item) {
            $data['items'][] = [
                'id' => $item->getId()->getId(),
                'menu' => $item->getMenu() ? $item->getMenu()->getId()->getId() : null,
                'position' => $item->getPosition(),
                'parent' => $item->getParent() ? $item->getParent()->getId()->getId() : null,
                'level' => $item->getLevel(),
                'type' => $item->getType(),
                'identity' => $item->getIdentity(),
                'hash' => $item->getHash(),
                'target' => $item->getTarget(),
                'locale' => $item->getLocale(),
                'name' => $item->getName(),
                'visibility' => $item->getVisibility(),
                'metadata' => $item->getMetadata(),
            ];
        }

        return $data;
    }
}
