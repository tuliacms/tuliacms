<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Renderer;

use Tulia\Cms\Widget\Ports\Domain\Renderer\RendererInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Widget\Configuration\ArrayConfiguration;
use Tulia\Component\Widget\Registry\WidgetRegistryInterface;
use Tulia\Component\Widget\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Renderer implements RendererInterface
{
    private StorageInterface $storage;

    private WidgetRegistryInterface $registry;

    private EngineInterface $engine;

    public function __construct(
        StorageInterface $storage,
        WidgetRegistryInterface $registry,
        EngineInterface $engine
    ) {
        $this->storage = $storage;
        $this->registry = $registry;
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function forId(string $id): string
    {
        $widget = $this->storage->findById($id);

        if ($widget === []) {
            return '';
        }

        $widget = $this->prepareWidgetData($widget);

        if (! $widget['visibility']) {
            return '';
        }

        return $this->render($widget);
    }

    /**
     * {@inheritdoc}
     */
    public function forSpace(string $space): string
    {
        $widgets = $this->storage->findBySpace($space);

        if ($widgets === []) {
            return '';
        }

        $result = [];

        foreach ($widgets as $widget) {
            $widget = $this->prepareWidgetData($widget);

            if (! $widget['visibility']) {
                continue;
            }

            $result[] = $this->render($widget);
        }

        return implode('', $result);
    }

    private function render(array $data): string
    {
        if ($this->registry->has($data['widget_type']) === false) {
            return '';
        }

        $config = new ArrayConfiguration($data['space']);
        $widget = $this->registry->get($data['widget_type']);
        $widget->configure($config);
        $config->merge(array_merge(
            $data['payload'],
            $data['payload_localized'],
        ));

        $view = $widget->render($config);

        if (! $view) {
            return '';
        }

        $classes = 'widget-item';
        $classes .= ' widget-item-outer';
        $classes .= ' widget-space-' . $data['space'];
        $classes .= ' widget-item-' . $data['id'];
        $classes .= ' ' . implode(' ', $data['styles']);
        $classes .= ' widget-' . str_replace('.', '-', strtolower($data['widget_type']));

        if ($data['html_class']) {
            $classes .= ' ' . $data['html_class'];
        }

        $attributes = [
            'class' => $classes,
        ];

        if ($data['html_id']) {
            $attributes['id'] = $data['html_id'];
        }

        $view->addData([
            'config' => $config,
            'widgetTitle' => $data['title'],
            'widgetAttributes' => $attributes,
        ]);

        return $this->engine->render($view);
    }

    private function prepareWidgetData(array $widget): array
    {
        $widget['visibility'] = (bool) $widget['visibility'];
        $widget['styles'] = (array) json_decode($widget['styles'], true);
        $widget['payload'] = (array) json_decode($widget['payload'], true);
        $widget['payload_localized'] = (array) json_decode($widget['payload_localized'], true);

        return $widget;
    }
}
