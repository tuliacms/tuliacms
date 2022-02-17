<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Attributes\Domain\WriteModel\AttributesRepository;
use Tulia\Cms\Menu\Domain\Metadata\Item\Enum\MetadataEnum;
use Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\MenuActionsChainInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemAdded;
use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemRemoved;
use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemUpdated;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuCreated;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuDeleted;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuUpdated;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Shared\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuRepository
{
    private MenuStorageInterface $storage;

    private UuidGeneratorInterface $uuidGenerator;

    private EventDispatcherInterface $eventDispatcher;

    private CurrentWebsiteInterface $currentWebsite;

    private AttributesRepository $metadataRepository;

    private MenuActionsChainInterface $actionsChain;

    public function __construct(
        MenuStorageInterface $storage,
        UuidGeneratorInterface $uuidGenerator,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite,
        AttributesRepository $metadataRepository,
        MenuActionsChainInterface $actionsChain
    ) {
        $this->storage = $storage;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->currentWebsite = $currentWebsite;
        $this->metadataRepository = $metadataRepository;
        $this->actionsChain = $actionsChain;
    }

    public function createNewMenu(): Menu
    {
        return Menu::create(
            $this->uuidGenerator->generate(),
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode()
        );
    }

    public function createNewItem(): Item
    {
        return Item::create(
            $this->uuidGenerator->generate(),
            $this->currentWebsite->getLocale()->getCode(),
            false
        );
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

        $metadata = $this->metadataRepository->findAllAggregated(MetadataEnum::MENUITEM_GROUP, array_column($data['items'], 'id'), []);
        $menu = Menu::buildFromArray($data);

        foreach ($data['items'] as $item) {
            $item['metadata'] = $metadata[$item['id']] ?? [];
            $menu->addItem(Item::buildFromArray($item));
        }

        // Reset items changes after create new Entity with data from storage.
        $menu->getItemsChanges();

        $this->actionsChain->execute('find', $menu);

        return $menu;
    }

    public function save(Menu $menu): void
    {
        $this->actionsChain->execute('save', $menu);

        $data = $this->extract($menu);
        $this->storage->beginTransaction();

        try {
            $this->storage->insert($data, $this->currentWebsite->getDefaultLocale()->getCode());

            foreach ($data['items'] as $item) {
                $this->metadataRepository->persist(
                    MetadataEnum::MENUITEM_GROUP,
                    $item['id'],
                    $item['metadata']
                );
            }

            $this->storage->commit();
        } catch (\Exception $e) {
            $this->storage->rollback();
            throw $e;
        }

        $this->eventDispatcher->dispatch(new MenuCreated($menu->getId()));
        $this->dispatchItemsEvents($data);
    }

    public function update(Menu $menu): void
    {
        $this->actionsChain->execute('update', $menu);

        $data = $this->extract($menu);

        /**
         * We dont want to overwrite root item in update. It must be created
         * only once, at the creating of the menu, and must not be updated forever.
         */
        $data = $this->removeRootItem($data);

        $this->storage->beginTransaction();

        try {
            $this->storage->update($data, $this->currentWebsite->getDefaultLocale()->getCode());

            foreach ($data['items'] as $item) {
                $this->metadataRepository->persist(
                    MetadataEnum::MENUITEM_GROUP,
                    $item['id'],
                    $item['metadata']
                );
            }

            $this->storage->commit();
        } catch (\Exception $e) {
            $this->storage->rollback();
            throw $e;
        }

        $this->eventDispatcher->dispatch(new MenuUpdated($menu->getId()));
        $this->dispatchItemsEvents($data);
    }

    public function delete(Menu $menu): void
    {
        $this->actionsChain->execute('delete', $menu);

        $this->storage->delete($menu->getId());
        $this->eventDispatcher->dispatch(new MenuDeleted($menu->getId()));
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

        foreach ($itemsChanges as $changeData) {
            /** @var Item $item */
            $item = $changeData['item'];
            $id = $item->getId();

            $data['items'][$id] = [
                '_change_type' => $changeData['type'],
                'id' => $id,
                'menu' => $item->getMenu() ? $item->getMenu()->getId() : null,
                'parent_id' => $item->getParentId() ?: null,
                'position' => $item->getPosition(),
                'level' => $item->getLevel(),
                'is_root' => $item->isRoot(),
                'type' => $item->getType(),
                'identity' => $item->getIdentity(),
                'hash' => $item->getHash(),
                'target' => $item->getTarget(),
                'locale' => $item->getLocale(),
                'name' => $item->getName(),
                'visibility' => $item->getVisibility(),
                'metadata' => $item->getAttributes(),
            ];
        }

        return $data;
    }

    private function removeRootItem(array $data): array
    {
        foreach ($data['items'] as $key => $item) {
            if ($item['is_root']) {
                unset($data['items'][$key]);
            }
        }

        return $data;
    }
}
