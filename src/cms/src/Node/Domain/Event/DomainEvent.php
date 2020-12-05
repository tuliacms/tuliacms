<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Event;

use Tulia\Cms\Node\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    /**
     * @var AggregateId
     */
    private $nodeId;

    /**
     * @param AggregateId $nodeId
     */
    public function __construct(AggregateId $nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return AggregateId
     */
    public function getNodeId(): AggregateId
    {
        return $this->nodeId;
    }
}
