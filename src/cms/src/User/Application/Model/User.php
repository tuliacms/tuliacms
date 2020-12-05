<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Model;

use Tulia\Cms\User\Query\Model\User as QueryModelUser;

/**
 * @author Adam Banaszkiewicz
 */
class User
{
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
    protected $metadata = [];

    public static function fromQueryModel(QueryModelUser $user): self
    {
        $self = new self($user->getId());
        $self->setUsername($user->getUsername());
        $self->setPassword($user->getPassword());
        $self->setEmail($user->getEmail());
        $self->setLocale($user->getLocale());
        $self->setRoles($user->getRoles());
        $self->setEnabled($user->getEnabled());
        $self->setAccountExpired($user->getAccountExpired());
        $self->setCredentialsExpired($user->getCredentialsExpired());
        $self->setAccountLocked($user->getAccountLocked());
        $self->setMetadata($user->getMetadata()->all());

        return $self;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->metadata[$name] ?? null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value): void
    {
        $this->metadata[$name] = $value;
    }

    /**
     * @param $name
     */
    public function __isset($name): bool
    {
        return true;
    }

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
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

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
