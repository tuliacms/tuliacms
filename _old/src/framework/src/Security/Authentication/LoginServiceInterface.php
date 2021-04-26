<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authentication;

use Tulia\Framework\Security\Authentication\Exception\LoginException;
use Tulia\Framework\Security\Authentication\LoginCredentials\LoginCredentialsInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface LoginServiceInterface
{
    /**
     * @param LoginCredentialsInterface $credentials
     *
     * @throws LoginException
     */
    public function login(LoginCredentialsInterface $credentials): void;
}
