<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\Event;

use Tulia\Cms\User\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class RoleWasGiven extends DomainEvent
{
    /**
     * @var string
     */
    private $role;

    public function __construct(AggregateId $userId, string $role)
    {
        parent::__construct($userId);

        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }
}
