<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class VisibilityChanged extends DomainEvent
{
    /**
     * @var bool
     */
    private $visibility;

    /**
     * @param AggregateId $nodeId
     * @param bool $visibility
     */
    public function __construct(AggregateId $nodeId, bool $visibility)
    {
        parent::__construct($nodeId);

        $this->visibility = $visibility;
    }

    /**
     * @return bool
     */
    public function getVisibility(): bool
    {
        return $this->visibility;
    }
}
