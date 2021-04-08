<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Event;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;

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
     * @param AggregateId $termId
     * @param null|string $slug
     */
    public function __construct(AggregateId $termId, ?string $slug)
    {
        parent::__construct($termId);

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
