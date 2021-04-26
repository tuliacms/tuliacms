<?php

declare(strict_types = 1);

namespace Tulia\Component\Widget\Registry;

use Tulia\Component\Widget\Exception\WidgetNotFoundException;
use Tulia\Component\Widget\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetRegistry implements WidgetRegistryInterface
{
    protected iterable $widgets = [];

    public function __construct(iterable $widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * {@inheritdoc}
     */
    public function add(WidgetInterface $widget): void
    {
        $this->widgets[] = $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(WidgetInterface $widget): void
    {
        foreach ($this->widgets as $key => $wgt) {
            if ($wgt === $widget) {
                unset($this->widgets[$key]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id): WidgetInterface
    {
        foreach ($this->widgets as $widget) {
            if ($widget->getId() === $id) {
                return $widget;
            }
        }

        throw new WidgetNotFoundException(sprintf('Widget %s not found.', $id));
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        foreach ($this->widgets as $widget) {
            if ($widget->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): iterable
    {
        return $this->widgets;
    }
}
