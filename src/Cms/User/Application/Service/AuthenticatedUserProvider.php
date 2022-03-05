<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderScopeEnum;
use Tulia\Cms\User\Domain\ReadModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class AuthenticatedUserProvider implements AuthenticatedUserProviderInterface
{
    private TokenStorageInterface $tokenStorage;
    private ?User $user = null;
    private UserFinderInterface $userFinder;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserFinderInterface $userFinder,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userFinder = $userFinder;
        $this->passwordHasher = $passwordHasher;
    }

    public function getUser(): User
    {
        $token = $this->tokenStorage->getToken();

        if (! $token) {
            $user = null;
        } else {
            if ($this->user) {
                return $this->user;
            }

            $user = $this->userFinder->findOne(['email' => $token->getUserIdentifier()], UserFinderScopeEnum::INTERNAL);
        }

        if (! $user) {
            $user = $this->getDefaultUser();
        }

        return $this->user = $user;
    }

    public function getDefaultUser(): User
    {
        return new User();
    }

    public function isPasswordValid(string $plaintextPassword): bool
    {
        $user = $this->getUser();
        $coreUser = new CoreUser($user->getEmail(), $user->getPassword(), $user->getRoles());

        return $this->passwordHasher->isPasswordValid($coreUser, $plaintextPassword);
    }
}
