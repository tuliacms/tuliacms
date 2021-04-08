<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Event;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class Renamed extends DomainEvent
{
    /**
     * @var null|string
     */
    private $name;

    /**
     * @param AggregateId $termId
     * @param null|string $name
     */
    public function __construct(AggregateId $termId, ?string $name)
    {
        parent::__construct($termId);

        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getSlug(): ?string
    {
        return $this->name;
    }
}
