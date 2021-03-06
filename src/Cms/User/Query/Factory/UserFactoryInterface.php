<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query\Factory;

use Tulia\Cms\User\Query\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
interface UserFactoryInterface
{
    /**
     * Creates new UserInterface object, with loaded metadata object (ready to sync),
     * and with default values given in $data array. Sets also object ID and is ready to
     * store in database.
     *
     * @param array $data
     *
     * @return User
     */
    public function createNew(array $data = []): User;
}
