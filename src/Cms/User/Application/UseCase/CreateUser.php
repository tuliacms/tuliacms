<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Event\UserPreCreateEvent;
use Tulia\Cms\User\Application\UseCase\Input\UserInput;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CreateUser
{
    private UserRepositoryInterface $repository;
    private EventBusInterface $eventDispatcher;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->passwordHasher = $passwordHasher;
    }

    public function __invoke(UserInput $input)
    {
        $securityUser = new CoreUser($input->email, $input->password, $input->roles);
        $hashedPassword = $this->passwordHasher->hashPassword($securityUser, $input->password);

        $user = User::create(
            $this->repository->generateNextId(),
            $input->email,
            $hashedPassword,
            $input->roles,
            $input->enabled,
            $input->locale,
            $input->attributes
        );

        dump($user);exit;

        $event = new UserPreCreateEvent($user);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->save($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());

        $this->eventDispatcher->dispatch(new UserCreatedEvent($user));
    }
}
