<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Command;

use Tulia\Cms\Menu\Application\Model\Item as ApplicationItem;
use Tulia\Cms\Menu\Domain\Menu\Exception\ItemNotFoundException;
use Tulia\Cms\Menu\Domain\Menu\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\Menu\Exception\ParentItemReccurencyException;
use Tulia\Cms\Menu\Domain\Menu\Model\Aggregate\Menu;
use Tulia\Cms\Menu\Domain\Menu\Model\Aggregate\Item;
use Tulia\Cms\Menu\Domain\Menu\Model\RepositoryInterface;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\ItemId;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ItemStorage
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var EventBusInterface
     */
    private $eventDispatcher;

    /**
     * @param RepositoryInterface $repository
     * @param EventBusInterface $eventDispatcher
     */
    public function __construct(RepositoryInterface $repository, EventBusInterface $eventDispatcher)
    {
        $this->repository      = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ApplicationItem $item
     *
     * @throws MenuNotFoundException
     * @throws ParentItemReccurencyException
     * @throws ItemNotFoundException
     */
    public function save(ApplicationItem $item): void
    {
        $menu = $this->repository->find(new AggregateId($item->getMenuId()));

        try {
            $entity = $menu->getItem(new ItemId($item->getId()));
        } catch (ItemNotFoundException $exception) {
            $entity = new Item(new ItemId($item->getId()), $item->getLocale());
        }

        $this->updateEntity($menu, $item, $entity);
        $menu->addItem($entity);

        $this->repository->save($menu);
        $this->eventDispatcher->dispatchCollection($menu->collectDomainEvents());
    }

    public function delete(ApplicationItem $item): void
    {
        $menu = $this->repository->find(new AggregateId($item->getMenuId()));
        $entity = $menu->getItem(new ItemId($item->getId()));
        $menu->removeItem($entity);

        $this->repository->save($menu);
        $this->eventDispatcher->dispatchCollection($menu->collectDomainEvents());
    }

    /**
     * @param Menu $menu
     * @param ApplicationItem $item
     * @param Item $entity
     *
     * @throws ParentItemReccurencyException
     * @throws ItemNotFoundException
     */
    private function updateEntity(Menu $menu, ApplicationItem $item, Item $entity): void
    {
        foreach ($item->getMetadata() as $key => $val) {
            $entity->changeMetadataValue($key, $val);
        }

        $entity->rename($item->getName());
        $entity->moveToPosition($item->getPosition());
        $entity->changeVisibility($item->getVisibility());
        $entity->changeType($item->getType());
        $entity->changeIdentity($item->getIdentity());
        $entity->changeHash($item->getHash());
        $entity->changeTarget($item->getTarget());

        if ($item->getParentId()) {
            $entity->assignToParent($menu->getItem(new ItemId($item->getParentId())));
        } else {
            $entity->assignToParent(null);
        }
    }
}
