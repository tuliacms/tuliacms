<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class PayloadUpdated extends DomainEvent
{
    /**
     * @var array
     */
    private $payload;

    /**
     * @param AggregateId $widgetId
     * @param array $payload
     */
    public function __construct(AggregateId $widgetId, array $payload)
    {
        parent::__construct($widgetId);

        $this->payload = $payload;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
