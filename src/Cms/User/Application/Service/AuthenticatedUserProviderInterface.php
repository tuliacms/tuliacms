<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Service;

use Tulia\Cms\User\Domain\ReadModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
interface AuthenticatedUserProviderInterface
{
    public function getUser(): User;

    public function getDefaultUser(): User;

    public function isPasswordValid(string $plaintextPassword): bool;
}
