<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Widgets;

/**
 * @author Adam Banaszkiewicz
 */
class DashboardWidgetRegistry
{
    /**
     * @var DashboardWidgetInterface[]
     */
    private $widgets;

    public function __construct(iterable $widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * @return DashboardWidgetInterface[]
     */
    public function all(): array
    {
        return iterator_to_array($this->widgets);
    }

    /**
     * @return DashboardWidgetInterface[]
     */
    public function allSupporting(string $group): array
    {
        $result = [];

        foreach ($this->widgets as $widget) {
            if ($widget->supports($group)) {
                $result[] = $widget;
            }
        }

        return $result;
    }
}
