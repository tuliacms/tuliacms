<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authentication\LoginCredentials;

/**
 * @author Adam Banaszkiewicz
 */
class LoginFormCredentials extends AbstractLoginCredentials
{
    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->set('username', (string) $username);
        $this->set('password', (string) $password);
    }
}
