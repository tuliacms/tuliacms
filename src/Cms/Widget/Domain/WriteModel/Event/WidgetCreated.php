<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Event;

use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetCreated extends DomainEvent
{
    public static function fromWidget(Widget $widget): self
    {
        return new self(
            $widget->getId()->getValue(),
            $widget->getWidgetType(),
            $widget->getWebsiteId(),
            $widget->getLocale()
        );
    }
}
