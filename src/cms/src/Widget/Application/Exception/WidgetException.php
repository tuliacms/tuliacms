<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\Exception;

use Tulia\Cms\Widget\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetException extends \Exception
{
    /**
     * @var WidgetInterface
     */
    protected $widget;

    /**
     * @param WidgetInterface $widget
     */
    public function setWidget(WidgetInterface $widget): void
    {
        $this->widget = $widget;
    }

    /**
     * @return WidgetInterface
     */
    public function getWidget(): WidgetInterface
    {
        return $this->widget;
    }
}
