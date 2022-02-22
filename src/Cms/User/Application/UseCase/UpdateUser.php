<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Event\UserPreUpdateEvent;
use Tulia\Cms\User\Application\UseCase\Traits\UserAttributesTrait;
use Tulia\Cms\User\Domain\WriteModel\Event\UserUpdated;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UpdateUser
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
    public function __invoke(User $user, array $attributes): void
    {
        $data = $this->flattenAttributes($attributes);
        $attributes = $this->removeModelsAttributes($attributes);

        $user->updateAttributes($attributes);
        $user->persistRoles($data['roles']);
        $user->changeLocale($data['locale']);

        if ($data['enabled']) {
            $user->enableAccount();
        } else {
            $user->disableAccount();
        }

        if (empty($data['password']) === false) {
            $securityUser = new CoreUser($data['email'], null, $data['roles']);
            $hashedPassword = $this->passwordHasher->hashPassword($securityUser, $data['password']);
            $user->changePassword($hashedPassword);
        }

        $event = UserPreUpdateEvent::fromModel($user);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());
        $this->eventDispatcher->dispatch(UserUpdated::fromModel($user));
    }
}
