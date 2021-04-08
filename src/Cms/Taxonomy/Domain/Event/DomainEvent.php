<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Event;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    /**
     * @var AggregateId
     */
    private $termId;

    /**
     * @param AggregateId $termId
     */
    public function __construct(AggregateId $termId)
    {
        $this->termId = $termId;
    }

    /**
     * @return AggregateId
     */
    public function getTermId(): AggregateId
    {
        return $this->termId;
    }
}
