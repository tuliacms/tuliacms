<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuUpdated;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractMenuUseCase
{
    protected MenuRepositoryInterface $repository;
    protected EventBusInterface $eventDispatcher;
    protected AggregateActionsChainInterface $actionsChain;

    public function __construct(
        MenuRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->actionsChain = $actionsChain;
    }

    protected function create(Menu $menu): void
    {
        $this->actionsChain->execute('create', $menu);

        try {
            $this->repository->save($menu);
            $this->eventDispatcher->dispatchCollection($menu->collectDomainEvents());
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    protected function update(Menu $menu): void
    {
        $this->actionsChain->execute('update', $menu);

        try {
            $this->repository->save($menu);
            $this->eventDispatcher->dispatchCollection($menu->collectDomainEvents());
            $this->eventDispatcher->dispatch(MenuUpdated::fromModel($menu));
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param Attribute[] $attributes
     */
    protected function flattenAttributes(array $attributes): array
    {
        $result = [];

        foreach ($attributes as $attribute) {
            if ($attribute instanceof Attribute) {
                $result[$attribute->getUri()] = $attribute->getValue();
            }
        }

        return $result;
    }

    /**
     * @param Attribute[] $attributes
     * @return Attribute[]
     */
    protected function removeMenuItemAttributes(array $attributes): array
    {
        unset(
            $attributes['name'],
            $attributes['type'],
            $attributes['visibility'],
            $attributes['identity'],
            $attributes['hash'],
            $attributes['target'],
        );

        return $attributes;
    }
}
