<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Domain\WriteModel\Event\UserUpdated;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractUserUserCase
{
    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->actionsChain = $actionsChain;
    }

    protected function create(User $user): void
    {
        $this->actionsChain->execute('update', $user);

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());
        $this->eventDispatcher->dispatch(UserUpdated::fromModel($user));
    }

    protected function update(User $user): void
    {
        $this->actionsChain->execute('update', $user);

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());
        $this->eventDispatcher->dispatch(UserUpdated::fromModel($user));
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
    protected function removeModelsAttributes(array $attributes): array
    {
        unset(
            $attributes['id'],
            $attributes['password'],
            $attributes['password_repeat'],
            $attributes['email'],
            $attributes['roles'],
            $attributes['enabled'],
            $attributes['locale'],
            $attributes['remove_avatar'],
        );

        return $attributes;
    }
}
