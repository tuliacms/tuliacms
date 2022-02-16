<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Model;

use Tulia\Cms\Attributes\Domain\WriteModel\MagickAttributesTrait;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\AttributesAwareInterface;
use Tulia\Cms\User\Query\Model\User as QueryModelUser;

/**
 * @author Adam Banaszkiewicz
 */
class User implements AttributesAwareInterface
{
    use MagickAttributesTrait;

    protected ?string $id = null;

    protected ?string $username = null;

    protected ?string $password = null;

    protected ?string $email = null;

    protected string $locale = 'en_US';

    protected bool $enabled = true;

    protected bool $accountExpired = false;

    protected bool $credentialsExpired = false;

    protected bool $accountLocked = false;

    protected array $roles = [];

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
        $self->replaceAttributes($user->getAttributes());

        return $self;
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
}
