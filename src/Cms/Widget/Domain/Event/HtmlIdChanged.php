<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class HtmlIdChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $htmlId;

    /**
     * @param AggregateId $widgetId
     * @param null|string $htmlId
     */
    public function __construct(AggregateId $widgetId, ?string $htmlId)
    {
        parent::__construct($widgetId);

        $this->htmlId = $htmlId;
    }

    /**
     * @return null|string
     */
    public function getHtmlId(): ?string
    {
        return $this->htmlId;
    }
}
