<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authentication\LoginCredentials;

/**
 * @author Adam Banaszkiewicz
 */
interface LoginCredentialsInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name);

    /**
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value): void;
}
