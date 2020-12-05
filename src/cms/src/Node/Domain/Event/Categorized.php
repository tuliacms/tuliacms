<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Event;

use Tulia\Cms\Node\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class Categorized extends DomainEvent
{
    /**
     * @var null|string
     */
    private $category;

    /**
     * @param AggregateId $nodeId
     * @param null|string $category
     */
    public function __construct(AggregateId $nodeId, ?string $category)
    {
        parent::__construct($nodeId);

        $this->category = $category;
    }

    /**
     * @return null|string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }
}
