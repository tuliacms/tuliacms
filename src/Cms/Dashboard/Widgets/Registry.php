<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Widgets;

/**
 * @author Adam Banaszkiewicz
 */
class Registry
{
    /**
     * @var iterable|array|WidgetInterface[]
     */
    private $widgets;

    public function __construct(iterable $widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * @return array|WidgetInterface[]
     */
    public function all(): array
    {
        $this->resolveIterable();

        return $this->widgets;
    }

    /**
     * @param string $group
     *
     * @return array|WidgetInterface[]
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

    private function resolveIterable(): void
    {
        foreach ($this->widgets as $widget) {
            // Do nothing...
        }
    }
}
