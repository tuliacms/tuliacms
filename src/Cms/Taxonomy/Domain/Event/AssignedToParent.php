<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Event;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;

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
     * @param AggregateId $termId
     * @param null|string $parentId
     */
    public function __construct(AggregateId $termId, ?string $parentId)
    {
        parent::__construct($termId);

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
