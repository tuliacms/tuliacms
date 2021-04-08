<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class MovedToSpace extends DomainEvent
{
    /**
     * @var string
     */
    private $space;

    /**
     * @param AggregateId $widgetId
     * @param string $space
     */
    public function __construct(AggregateId $widgetId, string $space)
    {
        parent::__construct($widgetId);

        $this->space = $space;
    }

    /**
     * @return string
     */
    public function getSpace(): string
    {
        return $this->space;
    }
}
