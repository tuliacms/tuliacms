<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class LocalizedPayloadUpdated extends DomainEvent
{
    /**
     * @var array
     */
    private $localizedPayload;

    /**
     * @param AggregateId $widgetId
     * @param array $localizedPayload
     */
    public function __construct(AggregateId $widgetId, array $localizedPayload)
    {
        parent::__construct($widgetId);

        $this->localizedPayload = $localizedPayload;
    }

    /**
     * @return array
     */
    public function getLocalizedPayload(): array
    {
        return $this->localizedPayload;
    }
}
