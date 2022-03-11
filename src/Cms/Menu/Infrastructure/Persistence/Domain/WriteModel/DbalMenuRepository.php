<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Attributes\Domain\WriteModel\AttributesRepository;
use Tulia\Cms\Menu\Domain\WriteModel\ActionsChain\MenuActionsChainInterface;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Shared\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMenuRepository implements MenuRepositoryInterface
{
    private DbalMenuStorage $storage;
    private UuidGeneratorInterface $uuidGenerator;
    private CurrentWebsiteInterface $currentWebsite;
    private AttributesRepository $attributesRepository;
    private MenuActionsChainInterface $actionsChain;

    public function __construct(
        DbalMenuStorage $storage,
        UuidGeneratorInterface $uuidGenerator,
        CurrentWebsiteInterface $currentWebsite,
        AttributesRepository $attributesRepository,
        MenuActionsChainInterface $actionsChain
    ) {
        $this->storage = $storage;
        $this->uuidGenerator = $uuidGenerator;
        $this->currentWebsite = $currentWebsite;
        $this->attributesRepository = $attributesRepository;
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

    public function createNewItem(Menu $menu): Item
    {
        return $menu->createNewItem($this->uuidGenerator->generate());
    }

    public function find(string $id): ?Menu
    {
        $data = $this->storage->find(
            $id,
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        if ($data === null) {
            return null;
        }

        $metadata = $this->attributesRepository->findAllAggregated('menu_item', array_column($data['items'], 'id'), []);

        foreach ($data['items'] as $key => $item) {
            $data['items'][$key]['metadata'] = $metadata[$item['id']] ?? [];
        }

        $menu = Menu::buildFromArray($data);

        $this->actionsChain->execute('find', $menu);

        return $menu;
    }

    public function save(Menu $menu): void
    {
        $data = $menu->toArray();
        $this->storage->beginTransaction();

        try {
            if ($this->storage->exists($menu->getId())) {
                $this->storage->update($data, $this->currentWebsite->getDefaultLocale()->getCode());
            } else {
                $this->storage->insert($data, $this->currentWebsite->getDefaultLocale()->getCode());
            }

            foreach ($data['items'] as $item) {
                $this->attributesRepository->persist(
                    'menu_item',
                    $item['id'],
                    $item['attributes']
                );
            }

            $this->storage->commit();
        } catch (\Exception $e) {
            $this->storage->rollback();
            throw $e;
        }
    }

    public function delete(Menu $menu): void
    {
        $this->storage->delete($menu->getId());
    }
}
