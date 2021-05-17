<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class SlugChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $slug;

    /**
     * @param AggregateId $nodeId
     * @param null|string $slug
     */
    public function __construct(AggregateId $nodeId, ?string $slug)
    {
        parent::__construct($nodeId);

        $this->slug = $slug;
    }

    /**
     * @return null|string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
