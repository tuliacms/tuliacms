<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateUser extends AbstractUserUseCase
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

        $this->create($user);

        return $user->getId()->getValue();
    }
}
