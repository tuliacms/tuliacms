<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

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
     * @param AggregateId $widgetId
     * @param bool $visibility
     */
    public function __construct(AggregateId $widgetId, bool $visibility)
    {
        parent::__construct($widgetId);

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
