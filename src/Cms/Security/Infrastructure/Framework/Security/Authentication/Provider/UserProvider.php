<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Infrastructure\Framework\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

use function is_array;

/**
 * @author Adam Banaszkiewicz
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * Simple cache for users.
     *
     * @var array
     */
    protected $users = [];

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username)
    {
        return $this->getUser($username);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $storedUser = $this->getUser($user->getUsername());

        return new User(
            $storedUser->getUsername(),
            $storedUser->getPassword(),
            $storedUser->getRoles(),
            $storedUser->isEnabled(),
            $storedUser->isAccountNonExpired(),
            $storedUser->isCredentialsNonExpired() && $storedUser->getPassword() === $user->getPassword(),
            $storedUser->isAccountNonLocked()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class): bool
    {
        return UserInterface::class === $class || User::class === $class;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    private function getUser(string $username): UserInterface
    {
        if (isset($this->users[$username])) {
            return $this->users[$username];
        }

        $result = $this->connection->fetchAllAssociative('SELECT * FROM #__user WHERE username = :username OR email = :username LIMIT 1', [
            'username' => $username
        ]);

        if ($result === []) {
            $ex = new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            $ex->setUsername($username);

            throw $ex;
        }

        try {
            $roles = json_decode($result[0]['roles']);

            if (!is_array($roles)) {
                $roles = [];
            }
        } catch (Throwable $e) {
            $roles = [];
        }

        $user = new User(
            $result[0]['username'],
            $result[0]['password'],
            $roles,
            (bool) $result[0]['enabled'],
            ! $result[0]['account_expired'],
            ! $result[0]['credentials_expired'],
            ! $result[0]['account_locked']
        );

        return $this->users[$user->getUsername()] = $user;
    }
}
