<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ChangePassword extends AbstractUserUserCase
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain,
        UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct($repository, $eventDispatcher, $actionsChain);

        $this->passwordHasher = $passwordHasher;
    }

    public function __invoke(User $user, string $newPassword): void
    {
        $securityUser = new CoreUser($user->getEmail(), null, $user->getRoles());
        $hashedPassword = $this->passwordHasher->hashPassword($securityUser, $newPassword);
        $user->changePassword($hashedPassword);

        $this->update($user);
    }
}
