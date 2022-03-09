<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateUser extends AbstractUserUseCase
{
    private UserPasswordHasherInterface $passwordHasher;
    private UploaderInterface $uploader;

    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain,
        UserPasswordHasherInterface $passwordHasher,
        UploaderInterface $uploader
    ) {
        parent::__construct($repository, $eventDispatcher, $actionsChain);

        $this->passwordHasher = $passwordHasher;
        $this->uploader = $uploader;
    }

    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(User $user, array $attributes): void
    {
        $data = $this->flattenAttributes($attributes);
        $attributes = $this->removeModelsAttributes($attributes);

        if ($data['remove_avatar'] && $data['avatar']) {
            $this->uploader->removeUploaded($data['avatar']);
        }

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

        $this->update($user);
    }
}
