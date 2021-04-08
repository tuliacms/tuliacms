<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    /**
     * @var AggregateId
     */
    private $widgetId;

    /**
     * @param AggregateId $widgetId
     */
    public function __construct(AggregateId $widgetId)
    {
        $this->widgetId = $widgetId;
    }

    /**
     * @return AggregateId
     */
    public function getWidgetId(): AggregateId
    {
        return $this->widgetId;
    }
}
