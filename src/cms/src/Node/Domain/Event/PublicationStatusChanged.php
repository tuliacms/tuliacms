<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Event;

use Tulia\Cms\Node\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class PublicationStatusChanged extends DomainEvent
{
    /**
     * @var string
     */
    private $status;

    /**
     * @param AggregateId $nodeId
     * @param string $status
     */
    public function __construct(AggregateId $nodeId, string $status)
    {
        parent::__construct($nodeId);

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
