<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Event;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;

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
     * @param AggregateId $termId
     * @param bool $visibility
     */
    public function __construct(AggregateId $termId, bool $visibility)
    {
        parent::__construct($termId);

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
