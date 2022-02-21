<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Model;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Tulia\Cms\Attributes\Domain\WriteModel\MagickAttributesTrait;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\AttributesAwareInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
class User extends AggregateRoot implements AttributesAwareInterface, PasswordAuthenticatedUserInterface
{
    use MagickAttributesTrait;

    private AggregateId $id;

    protected string $password;

    protected string $email;

    protected string $locale = 'en_US';

    protected bool $enabled = true;

    protected bool $accountExpired = false;

    protected bool $credentialsExpired = false;

    protected bool $accountLocked = false;

    protected array $roles = [];

    private function __construct(AggregateId $id, string $email, string $password, array $roles)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
    }

    public static function create(
        AggregateId $id,
        string $email,
        string $password,
        array $roles,
        bool $enabled = true,
        string $locale = 'en_US',
        array $attributes = []
    ): self {
        $self = new self($id, $email, $password, $roles);
        $self->enabled = $enabled;
        $self->locale = $locale;
        $self->attributes = $attributes;
        $self->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\UserCreated($id));

        return $self;
    }

    public function getId(): AggregateId
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function changeAttributeValue(string $name, $value): void
    {
        if (\array_key_exists($name, $this->attributes)) {
            if ($this->attributes[$name] !== $value) {
                if (empty($value)) {
                    $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\AttributeValueDeleted($this->id, $name, $value));
                } else {
                    $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\AttributeValueChanged($this->id, $name, $value));
                }

                $this->attributes[$name] = $value;
            }
        } else {
            if (empty($value) === false) {
                $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\AttributeValueChanged($this->id, $name, $value));
                $this->attributes[$name] = $value;
            }
        }
    }

    /**
     * @param string $role
     */
    public function giveARole(string $role): void
    {
        if (in_array($role, $this->roles) === false) {
            $this->roles[] = $role;

            $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\RoleWasGiven($this->id, $role));
        }
    }

    /**
     * @param string $role
     */
    public function takeARole(string $role): void
    {
        $key = array_search($role, $this->roles);

        if ($key !== false) {
            unset($this->roles[$key]);

            $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\RoleWasTaken($this->id, $role));
        }
    }

    /**
     * @param array $roles
     */
    public function persistRoles(array $roles): void
    {
        $new = array_diff($roles, $this->roles);
        $old = array_diff($this->roles, $roles);

        foreach ($new as $role) {
            $this->giveARole($role);
        }

        foreach ($old as $role) {
            $this->takeARole($role);
        }
    }

    /**
     * @param string $password
     */
    public function changePassword(string $password): void
    {
        /**
         * Password cannot be empty. If empty password provided, user do
         * not want to update it. Security layer updated password only when
         * it's provided.
         */
        if ($this->password !== $password && empty($password) === false) {
            $this->password = $password;

            $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\PasswordChanged($this->id));
        }
    }

    /**
     * @param string $email
     *
     * @throws \Tulia\Cms\User\Domain\WriteModel\Exception\EmailEmptyException
     * @throws \Tulia\Cms\User\Domain\WriteModel\Exception\EmailInvalidException
     */
    public function changeEmail(string $email): void
    {
        if (empty($email)) {
            throw new \Tulia\Cms\User\Domain\WriteModel\Exception\EmailEmptyException('Email address must be provided, cannot be empty.');
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \Tulia\Cms\User\Domain\WriteModel\Exception\EmailInvalidException('Email address is invalid.');
        }

        if ($this->email !== $email) {
            $this->email = $email;

            $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\EmailChanged($this->id, $email));
        }
    }

    /**
     * @param string $locale
     */
    public function changeLocale(string $locale): void
    {
        if ($this->locale !== $locale) {
            $this->locale = $locale;

            $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\LocaleChanged($this->id, $locale));
        }
    }

    public function disableAccount(): void
    {
        if ($this->enabled) {
            $this->enabled = false;

            $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\AccountWasDisabled($this->id));
        }
    }

    public function enableAccount(): void
    {
        if (!$this->enabled) {
            $this->enabled = true;

            $this->recordThat(new \Tulia\Cms\User\Domain\WriteModel\Event\AccountWasEnabled($this->id));
        }
    }
}
