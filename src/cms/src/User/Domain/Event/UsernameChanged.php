<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\Event;

use Tulia\Cms\User\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class UsernameChanged extends DomainEvent
{
    /**
     * @var string
     */
    private $username;

    /**
     * @param AggregateId $userId
     * @param string $username
     */
    public function __construct(AggregateId $userId, string $username)
    {
        parent::__construct($userId);

        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
