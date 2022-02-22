<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Event\UserPreCreateEvent;
use Tulia\Cms\User\Application\UseCase\Traits\UserAttributesTrait;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CreateUser
{
    use UserAttributesTrait;

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

    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(array $attributes): ?string
    {
        $data = $this->flattenAttributes($attributes);
        $attributes = $this->removeModelsAttributes($attributes);

        $securityUser = new CoreUser($data['email'], null, $data['roles']);
        $hashedPassword = $this->passwordHasher->hashPassword($securityUser, $data['password']);

        $user = User::create(
            $this->repository->generateNextId(),
            $data['email'],
            $hashedPassword,
            $data['roles'],
            (bool) $data['enabled'],
            $data['locale'],
            $attributes
        );

        $event = UserPreCreateEvent::fromModel($user);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return null;
        }

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());

        return $user->getId()->getValue();
    }
}
