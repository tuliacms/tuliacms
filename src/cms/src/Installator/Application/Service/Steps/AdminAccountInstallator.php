<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\Application\Service\Steps;

use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Cms\User\Application\Command\UserStorage;
use Tulia\Cms\User\Application\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class AdminAccountInstallator
{
    /**
     * @var UserStorage
     */
    private $userStorage;
    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGenerator;

    public function __construct(UserStorage $userStorage, UuidGeneratorInterface $uuidGenerator)
    {
        $this->userStorage = $userStorage;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function install(array $credentials, string $locale): void
    {
        $user = new User($this->uuidGenerator->generate());
        $user->setEmail($credentials['email']);
        $user->setPassword($credentials['password']);
        $user->setUsername($credentials['username']);
        $user->setLocale($locale);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEnabled(true);
        $user->setAccountExpired(false);
        $user->setCredentialsExpired(false);
        $user->setAccountLocked(false);

        $this->userStorage->save($user);
    }
}
