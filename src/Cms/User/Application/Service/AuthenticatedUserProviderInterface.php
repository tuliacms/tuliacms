<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Service;

use Tulia\Cms\User\Query\Exception\QueryException;
use Tulia\Cms\User\Query\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
interface AuthenticatedUserProviderInterface
{
    /**
     * @return User
     *
     * @throws QueryException
     */
    public function getUser(): User;

    /**
     * @return User
     */
    public function getDefaultUser(): User;
}
