<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

use Tulia\Cms\User\Domain\WriteModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class UserUpdated extends DomainEvent
{
    public static function fromModel(User $user): self
    {
        return new self($user->getId()->getValue());
    }
}
