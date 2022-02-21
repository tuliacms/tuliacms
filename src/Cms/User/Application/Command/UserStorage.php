<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Command;

use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Event\UserCreatedEvent;
use Tulia\Cms\User\Application\Event\UserDeletedEvent;
use Tulia\Cms\User\Application\Event\UserPreCreateEvent;
use Tulia\Cms\User\Application\Event\UserPreDeleteEvent;
use Tulia\Cms\User\Application\Event\UserPreUpdateEvent;
use Tulia\Cms\User\Application\Event\UserUpdatedEvent;
use Tulia\Cms\User\Application\Model\User as ApplicationUser;
use Tulia\Cms\User\Domain\WriteModel\Event\UserDeleted;
use Tulia\Cms\User\Domain\WriteModel\Exception\UserNotFoundException;
use Tulia\Cms\User\Domain\WriteModel\Model\AggregateId;
use Tulia\Cms\User\Domain\WriteModel\Model\User as Aggregate;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UserStorage
{
    private $repository;

    private $eventDispatcher;

    public function __construct(UserRepositoryInterface $repository, EventBusInterface $eventDispatcher)
    {
        $this->repository      = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(ApplicationUser $user): void
    {
        $aggregateExists = false;

        try {
            $aggregate = $this->repository->find(new AggregateId($user->getId()));

            // We can assign $aggregateExists only after call find() in repository,
            // to handle exception when node not exists, and perform proper action when node not exists.
            $aggregateExists = true;
        } catch (UserNotFoundException $exception) {
            $aggregate = new Aggregate(new AggregateId($user->getId()));
        }

        if ($aggregateExists) {
            $event = new UserPreUpdateEvent($user);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        } else {
            $event = new UserPreCreateEvent($user);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        }

        $this->updateAggregate($user, $aggregate);

        $this->repository->save($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());

        if ($aggregateExists) {
            $this->eventDispatcher->dispatch(new UserUpdatedEvent($user));
        } else {
            $this->eventDispatcher->dispatch(new UserCreatedEvent($user));
        }
    }

    public function delete(ApplicationUser $user): void
    {
        try {
            $aggregate = $this->repository->find(new AggregateId($user->getId()));
        } catch (UserNotFoundException $exception) {
            return;
        }

        $event = new UserPreDeleteEvent($user);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->delete($aggregate);
        $this->eventDispatcher->dispatch(new UserDeleted($aggregate->getId()));
        $this->eventDispatcher->dispatch(new UserDeletedEvent($user));
    }

    private function updateAggregate(ApplicationUser $user, Aggregate $aggregate): void
    {
        foreach ($user->getAttributes() as $key => $val) {
            $aggregate->changeAttributeValue($key, $val);
        }

        if ($user->getPassword()) {
            $aggregate->changePassword($user->getPassword());
        }

        if ($user->getEnabled()) {
            $aggregate->enableAccount();
        } else {
            $aggregate->disableAccount();
        }

        $aggregate->persistRoles($user->getRoles());
        $aggregate->changeUsername($user->getUsername());
        $aggregate->changeEmail($user->getEmail());
        $aggregate->changeLocale($user->getLocale());
    }
}
