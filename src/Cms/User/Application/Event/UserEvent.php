<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\User\Application\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class UserEvent extends Event
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
