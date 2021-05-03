<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Menu\Application\Query\Finder\Factory\MenuFactory;
use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemAdded;
use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemRemoved;
use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemUpdated;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuCreated;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuDeleted;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuUpdated;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\MenuStorageInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuRepository implements MenuRepositoryInterface
{
    private MenuStorageInterface $storage;
    private MenuFactory $menuFactory;
    private EventDispatcherInterface $eventDispatcher;
    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        MenuStorageInterface $storage,
        MenuFactory $menuFactory,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->storage = $storage;
        $this->menuFactory = $menuFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->currentWebsite = $currentWebsite;
    }

    public function createNewMenu(array $data = []): Menu
    {
        return $this->menuFactory->createNewMenu($data);
    }

    public function createNewItem(array $data = []): Item
    {
        return $this->menuFactory->createNewItem($data);
    }

    /**
     * @throws MenuNotFoundException
     */
    public function find(string $id): Menu
    {
        $data = $this->storage->find(
            $id,
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        if ($data === null) {
            throw new MenuNotFoundException(sprintf('Menu %s not found.', $id));
        }

        $menu = Menu::buildFromArray($data);

        foreach ($data['items'] as $item) {
            $menu->addItem(Item::buildFromArray($item));
        }

        // Reset items changes after create new Entity with data from storage.
        $menu->getItemsChanges();

        return $menu;
    }

    public function save(Menu $menu): void
    {
        $data = $this->extract($menu);
        $this->storage->beginTransaction();

        try {
            $this->storage->insert($data, $this->currentWebsite->getDefaultLocale()->getCode());
            $this->storage->commit();
        } catch (\Exception $e) {
            $this->storage->rollback();
        }

        $this->eventDispatcher->dispatch(new MenuCreated($menu->getId()));
        $this->dispatchItemsEvents($data);
    }

    public function update(Menu $menu): void
    {
        $data = $this->extract($menu);
        $this->storage->beginTransaction();

        try {
            $this->storage->update($data, $this->currentWebsite->getDefaultLocale()->getCode());
            $this->storage->commit();
        } catch (\Exception $e) {
            $this->storage->rollback();
        }

        $this->eventDispatcher->dispatch(new MenuUpdated($menu->getId()));
        $this->dispatchItemsEvents($data);
    }

    public function delete(string $id): void
    {
        $this->storage->delete($id);
        $this->eventDispatcher->dispatch(new MenuDeleted($id));
    }

    private function dispatchItemsEvents(array $data): void
    {
        foreach ($data['items'] as $item) {
            if ($item['_change_type'] === 'add') {
                $this->eventDispatcher->dispatch(new ItemAdded($data['id'], $item['id']));
            } elseif ($item['_change_type'] === 'remove') {
                $this->eventDispatcher->dispatch(new ItemRemoved($data['id'], $item['id']));
            } else {
                $this->eventDispatcher->dispatch(new ItemUpdated($data['id'], $item['id']));
            }
        }
    }

    private function extract(Menu $menu): array
    {
        $data = [];
        $data['id'] = $menu->getId();
        $data['name'] = $menu->getName();
        $data['website_id'] = $menu->getWebsiteId();
        $data['items'] = [];

        $itemsChanges = $menu->getItemsChanges();

        foreach ($menu->getItems() as $item) {
            $id = $item->getId();

            $changeType = null;

            foreach ($itemsChanges as $change) {
                if ($change['id'] === $id) {
                    $changeType = $change['type'];
                }
            }

            // If nothing change, skip this item
            if ($changeType === null) {
                continue;
            }

            $data['items'][$id] = [
                '_change_type' => $changeType,
                'id' => $id,
                'menu' => $item->getMenu() ? $item->getMenu()->getId() : null,
                'position' => $item->getPosition(),
                'parent' => $item->getParentId() ?: null,
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
