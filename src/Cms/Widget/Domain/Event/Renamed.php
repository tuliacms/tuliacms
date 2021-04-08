<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class Renamed extends DomainEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param AggregateId $widgetId
     * @param string $name
     */
    public function __construct(AggregateId $widgetId, string $name)
    {
        parent::__construct($widgetId);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
