<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\Event;

use Tulia\Cms\User\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class EmailChanged extends DomainEvent
{
    /**
     * @var string
     */
    private $email;

    /**
     * @param AggregateId $userId
     * @param string $email
     */
    public function __construct(AggregateId $userId, string $email)
    {
        parent::__construct($userId);

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
