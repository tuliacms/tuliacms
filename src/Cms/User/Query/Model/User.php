<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query\Model;

use Tulia\Cms\Attributes\Domain\ReadModel\MagickAttributesTrait;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\AttributesAwareInterface;

/**
 * @author Adam Banaszkiewicz
 */
class User implements AttributesAwareInterface
{
    use MagickAttributesTrait;

    protected string $id;

    protected string $username;

    protected string $password;

    protected string $email;

    protected string $locale = 'en_US';

    protected bool $enabled = true;

    protected bool $accountExpired = false;

    protected bool $credentialsExpired = false;

    protected bool $accountLocked = false;

    protected array $roles = [];

    protected static array $fields = [
        'id'                  => 'id',
        'username'            => 'username',
        'password'            => 'password',
        'email'               => 'email',
        'roles'               => 'roles',
        'locale'              => 'locale',
        'enabled'             => 'enabled',
        'account_expired'     => 'accountExpired',
        'credentials_expired' => 'credentialsExpired',
        'account_locked'      => 'accountLocked',
    ];

    /**
     * {@inheritdoc}
     */
    public static function buildFromArray(array $data): self
    {
        $user = new self();

        if (isset($data['id']) === false) {
            throw new \InvalidArgumentException('User ID must be provided.');
        }

        $roles = $data['roles'] ?? [];

        if (\is_string($roles)) {
            $roles = @ json_decode($roles, true);

            if (! $roles) {
                $roles = [];
            }
        }

        $user->setId($data['id']);
        $user->setUsername($data['username'] ?? null);
        $user->setPassword($data['password'] ?? null);
        $user->setEmail($data['email'] ?? null);
        $user->setLocale($data['locale'] ?? 'en_US');
        $user->setRoles($roles);
        $user->setEnabled((bool) ($data['enabled'] ?? false));
        $user->setAccountExpired((bool) ($data['account_expired'] ?? false));
        $user->setCredentialsExpired((bool) ($data['credentials_expired'] ?? false));
        $user->setAccountLocked((bool) ($data['account_locked'] ?? false));
        $user->replaceAttributes($data['metadata'] ?? []);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $params = []): array
    {
        $params = array_merge([
            'skip' => [],
        ], $params);

        $result = [];

        foreach (static::$fields as $key => $property) {
            $result[$key] = $this->{$property};
        }

        foreach ($params['skip'] as $skip) {
            unset($result[$skip]);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountExpired(): bool
    {
        return $this->accountExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountExpired(bool $accountExpired): void
    {
        $this->accountExpired = $accountExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentialsExpired(): bool
    {
        return $this->credentialsExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function setCredentialsExpired(bool $credentialsExpired): void
    {
        $this->credentialsExpired = $credentialsExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountLocked(): bool
    {
        return $this->accountLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountLocked(bool $accountLocked): void
    {
        $this->accountLocked = $accountLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
