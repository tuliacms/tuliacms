<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\User\Domain\WriteModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class UserEvent extends Event
{
    protected $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public static function fromModel(User $user): self
    {
        return new self($user->getId()->getValue());
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
