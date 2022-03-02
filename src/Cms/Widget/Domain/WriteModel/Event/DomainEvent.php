<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    private string $widgetId;
    private string $widgetType;
    private string $websiteId;
    private string $locale;

    public function __construct(string $widgetId, string $widgetType, string $websiteId, string $locale)
    {
        $this->widgetId = $widgetId;
        $this->widgetType = $widgetType;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public function getWidgetId(): string
    {
        return $this->widgetId;
    }

    public function getWidgetType(): string
    {
        return $this->widgetType;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
