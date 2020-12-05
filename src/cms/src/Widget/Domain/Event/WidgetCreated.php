<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetCreated extends DomainEvent
{
    /**
     * @var string
     */
    private $widgetTypeId;

    /**
     * @var string
     */
    private $websiteId;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param AggregateId $widgetId
     * @param string $widgetTypeId
     * @param string $websiteId
     * @param string $locale
     */
    public function __construct(AggregateId $widgetId, string $widgetTypeId, string $websiteId, string $locale)
    {
        parent::__construct($widgetId);

        $this->widgetTypeId = $widgetTypeId;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getWidgetTypeId(): string
    {
        return $this->widgetTypeId;
    }

    /**
     * @return string
     */
    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
