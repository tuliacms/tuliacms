<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Security\Core\User;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Adam Banaszkiewicz
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private string $email;
    private ?string $password;
    private array $roles;
    private ?string $salt;

    public function __construct(string $email, ?string $password, array $roles, ?string $salt = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->salt = $salt;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
    }
}
