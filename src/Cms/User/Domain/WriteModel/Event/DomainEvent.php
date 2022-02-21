<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    private string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
