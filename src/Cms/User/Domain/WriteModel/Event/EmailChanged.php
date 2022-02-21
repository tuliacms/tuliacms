<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class EmailChanged extends DomainEvent
{
    private string $email;

    public function __construct(string $userId, string $email)
    {
        parent::__construct($userId);

        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
