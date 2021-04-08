<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Widget\Application\Model\Widget;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetEvent extends Event
{
    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @param Widget $widget
     */
    public function __construct(Widget $widget)
    {
        $this->widget = $widget;
    }

    /**
     * @return Widget
     */
    public function getWidget(): Widget
    {
        return $this->widget;
    }
}
