<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase\Input;

/**
 * @author Adam Banaszkiewicz
 */
class UserInput
{
    public string $username = '';
    public string $password = '';
    public string $email = '';
    public string $locale = '';
    public bool $enabled = true;
    public bool $account_expired = false;
    public bool $credentials_expired = false;
    public bool $account_locked = false;
    public array $roles = [];
    public array $attributes = [];

    public function __construct(
        string $username,
        string $password,
        string $email
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }
}
