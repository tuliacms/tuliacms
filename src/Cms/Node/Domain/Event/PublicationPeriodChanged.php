<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Event;

use Tulia\Cms\Node\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class PublicationPeriodChanged extends DomainEvent
{
    /**
     * @var ImmutableDateTime
     */
    private $from;

    /**
     * @var ImmutableDateTime|null
     */
    private $to;

    /**
     * @param AggregateId $nodeId
     * @param ImmutableDateTime $from
     * @param ImmutableDateTime|null $to
     */
    public function __construct(AggregateId $nodeId, ImmutableDateTime $from, ?ImmutableDateTime $to = null)
    {
        parent::__construct($nodeId);

        $this->from = $from;
        $this->to   = $to;
    }

    /**
     * @return ImmutableDateTime
     */
    public function getFrom(): ImmutableDateTime
    {
        return $this->from;
    }

    /**
     * @return ImmutableDateTime|null
     */
    public function getTo(): ?ImmutableDateTime
    {
        return $this->to;
    }
}
