<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\Event;

use Tulia\Cms\User\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\WriteModel\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    /**
     * @var AggregateId
     */
    private $userId;

    /**
     * @param AggregateId $userId
     */
    public function __construct(AggregateId $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return AggregateId
     */
    public function getUserId(): AggregateId
    {
        return $this->userId;
    }
}
