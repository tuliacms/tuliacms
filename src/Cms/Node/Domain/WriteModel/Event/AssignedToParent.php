<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class AssignedToParent extends DomainEvent
{
    /**
     * @var null|string
     */
    private $parentId;

    /**
     * @param AggregateId $nodeId
     * @param null|string $parentId
     */
    public function __construct(AggregateId $nodeId, ?string $parentId)
    {
        parent::__construct($nodeId);

        $this->parentId = $parentId;
    }

    /**
     * @return null|string
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }
}
