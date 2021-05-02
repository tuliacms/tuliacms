<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Command;

use Tulia\Cms\Menu\Application\Event\MenuCreatedEvent;
use Tulia\Cms\Menu\Application\Event\MenuDeletedEvent;
use Tulia\Cms\Menu\Application\Event\MenuPreCreateEvent;
use Tulia\Cms\Menu\Application\Event\MenuPreDeleteEvent;
use Tulia\Cms\Menu\Application\Event\MenuPreUpdateEvent;
use Tulia\Cms\Menu\Application\Event\MenuUpdatedEvent;
use Tulia\Cms\Menu\Application\Model\Menu as ApplicationMenu;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Aggregate\Menu as Aggregate;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuDeleted;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Model\MenuRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\MenuId;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuStorage
{
    /**
     * @var MenuRepositoryInterface
     */
    private $repository;

    /**
     * @var EventBusInterface
     */
    private $eventDispatcher;

    /**
     * @param MenuRepositoryInterface $repository
     * @param EventBusInterface $eventDispatcher
     */
    public function __construct(MenuRepositoryInterface $repository, EventBusInterface $eventDispatcher)
    {
        $this->repository      = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(ApplicationMenu $menu): void
    {
        $aggregateExists = false;

        try {
            $aggregate = $this->repository->find(new MenuId($menu->getId()));

            // We can assign $aggregateExists only after call find() in repository,
            // to handle exception when node not exists, and perform proper action when node not exists.
            $aggregateExists = true;
        } catch (MenuNotFoundException $exception) {
            $aggregate = new Aggregate(
                new MenuId($menu->getId()),
                $menu->getWebsiteId()
            );
        }

        if ($aggregateExists) {
            $event = new MenuPreUpdateEvent($menu);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        } else {
            $event = new MenuPreCreateEvent($menu);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        }

        $this->updateAggregate($menu, $aggregate);

        $this->repository->save($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());

        if ($aggregateExists) {
            $this->eventDispatcher->dispatch(new MenuUpdatedEvent($menu));
        } else {
            $this->eventDispatcher->dispatch(new MenuCreatedEvent($menu));
        }
    }

    public function delete(ApplicationMenu $menu): void
    {
        try {
            $aggregate = $this->repository->find(new MenuId($menu->getId()));
        } catch (MenuNotFoundException $exception) {
            return;
        }

        $event = new MenuPreDeleteEvent($menu);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->delete($aggregate);
        $this->eventDispatcher->dispatch(new MenuDeleted($aggregate->getId()));
        $this->eventDispatcher->dispatch(new MenuDeletedEvent($menu));
    }

    private function updateAggregate(ApplicationMenu $menu, Aggregate $aggregate): void
    {
        $aggregate->rename($menu->getName());
    }
}
