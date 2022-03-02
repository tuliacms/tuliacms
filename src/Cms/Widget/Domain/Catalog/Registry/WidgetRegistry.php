<?php

declare(strict_types = 1);

namespace Tulia\Cms\Widget\Domain\Catalog\Registry;

use Tulia\Cms\Widget\Domain\Catalog\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\Catalog\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetRegistry implements WidgetRegistryInterface
{
    /** @var WidgetInfo[] */
    protected array $widgets = [];

    public function addWidget(array $info, WidgetInterface $instance)
    {
        $info['instance'] = $instance;

        $this->widgets[$info['id']] = WidgetInfo::fromArray($info);
    }

    public function get(string $id): WidgetInfo
    {
        foreach ($this->widgets as $widget) {
            if ($widget->getId() === $id) {
                return $widget;
            }
        }

        throw new WidgetNotFoundException(sprintf('Widget %s not found.', $id));
    }

    public function has(string $id): bool
    {
        foreach ($this->widgets as $widget) {
            if ($widget->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    public function all(): iterable
    {
        return $this->widgets;
    }
}
