<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\Aggregate;

use Tulia\Cms\Attributes\Domain\WriteModel\MagickAttributesTrait;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\AttributesAwareInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Cms\User\Domain\Event;
use Tulia\Cms\User\Domain\Exception;
use Tulia\Cms\User\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class User extends AggregateRoot implements AttributesAwareInterface
{
    use MagickAttributesTrait;

    private AggregateId $id;

    protected string $username;

    protected string $password;

    protected string $email;

    protected string $locale = 'en_US';

    protected bool $enabled = true;

    protected bool $accountExpired = false;

    protected bool $credentialsExpired = false;

    protected bool $accountLocked = false;

    protected array $roles = [];

    public function __construct(AggregateId $id)
    {
        $this->id = $id;

        $this->recordThat(new Event\UserCreated($id));
    }

    public function getId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function changeMetadataValue(string $name, $value): void
    {
        if (\array_key_exists($name, $this->attributes)) {
            if ($this->attributes[$name] !== $value) {
                if (empty($value)) {
                    $this->recordThat(new Event\MetadataValueDeleted($this->id, $name, $value));
                } else {
                    $this->recordThat(new Event\MetadataValueChanged($this->id, $name, $value));
                }

                $this->attributes[$name] = $value;
            }
        } else {
            if (empty($value) === false) {
                $this->recordThat(new Event\MetadataValueChanged($this->id, $name, $value));
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

            $this->recordThat(new Event\RoleWasGiven($this->id, $role));
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

            $this->recordThat(new Event\RoleWasTaken($this->id, $role));
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

            $this->recordThat(new Event\PasswordChanged($this->id));
        }
    }

    /**
     * @param string $username
     *
     * @throws Exception\UsernameEmptyException
     */
    public function changeUsername(string $username): void
    {
        if (empty($username)) {
            throw new Exception\UsernameEmptyException('Username must be provided, cannot be empty.');
        }

        if ($this->username !== $username) {
            $this->username = $username;

            $this->recordThat(new Event\UsernameChanged($this->id, $username));
        }
    }

    /**
     * @param string $email
     *
     * @throws Exception\EmailEmptyException
     * @throws Exception\EmailInvalidException
     */
    public function changeEmail(string $email): void
    {
        if (empty($email)) {
            throw new Exception\EmailEmptyException('Email address must be provided, cannot be empty.');
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new Exception\EmailInvalidException('Email address is invalid.');
        }

        if ($this->email !== $email) {
            $this->email = $email;

            $this->recordThat(new Event\EmailChanged($this->id, $email));
        }
    }

    /**
     * @param string $locale
     */
    public function changeLocale(string $locale): void
    {
        if ($this->locale !== $locale) {
            $this->locale = $locale;

            $this->recordThat(new Event\LocaleChanged($this->id, $locale));
        }
    }

    public function disableAccount(): void
    {
        if ($this->enabled) {
            $this->enabled = false;

            $this->recordThat(new Event\AccountWasDisabled($this->id));
        }
    }

    public function enableAccount(): void
    {
        if (!$this->enabled) {
            $this->enabled = true;

            $this->recordThat(new Event\AccountWasEnabled($this->id));
        }
    }
}
