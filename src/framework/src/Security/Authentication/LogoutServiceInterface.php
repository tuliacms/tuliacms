<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authentication;

/**
 * @author Adam Banaszkiewicz
 */
interface LogoutServiceInterface
{
    public function logout(): void;
}
