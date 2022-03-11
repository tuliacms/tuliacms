<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuDeleted;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteMenu
{
    private MenuRepositoryInterface $repository;
    private EventBusInterface $eventDispatcher;
    private AggregateActionsChainInterface $actionsChain;

    public function __construct(
        MenuRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->actionsChain = $actionsChain;
    }

    public function __invoke(Menu $menu): void
    {
        $this->actionsChain->execute('delete', $menu);

        try {
            $this->repository->delete($menu);
            $this->eventDispatcher->dispatch(MenuDeleted::fromModel($menu));
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
