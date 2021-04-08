<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class StyleWasRemoved extends DomainEvent
{
    /**
     * @var string
     */
    private $style;

    /**
     * @param AggregateId $widgetId
     * @param string $style
     */
    public function __construct(AggregateId $widgetId, string $style)
    {
        parent::__construct($widgetId);

        $this->style = $style;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }
}
