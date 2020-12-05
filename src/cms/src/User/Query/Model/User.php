<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query\Model;

use Tulia\Cms\Metadata\MagickMetadataTrait;
use Tulia\Cms\Metadata\Metadata;
use Tulia\Cms\Metadata\MetadataTrait;

/**
 * @author Adam Banaszkiewicz
 */
class User
{
    use MagickMetadataTrait;
    use MetadataTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $locale = 'en_US';

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var bool
     */
    protected $accountExpired = false;

    /**
     * @var bool
     */
    protected $credentialsExpired = false;

    /**
     * @var bool
     */
    protected $accountLocked = false;

    /**
     * @var array
     */
    protected $roles = [];

    /**
     * @var array
     */
    protected static $fields = [
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

        $user->setMetadata(new Metadata($data['metadata'] ?? []));

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

        /* foreach($this->getMetadata()->all() as $key => $val) {
            $result[$key] = $val;
        }*/

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
