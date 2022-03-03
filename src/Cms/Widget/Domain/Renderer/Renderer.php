<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Renderer;

use Tulia\Cms\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ArrayConfiguration;
use Tulia\Cms\Widget\Domain\Catalog\Registry\WidgetRegistryInterface;
use Tulia\Cms\Widget\Domain\Catalog\Storage\StorageInterface;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Renderer implements RendererInterface
{
    private StorageInterface $storage;
    private WidgetRegistryInterface $registry;
    private EngineInterface $engine;
    private AttributesFinder $attributeFinder;

    public function __construct(
        StorageInterface $storage,
        WidgetRegistryInterface $registry,
        EngineInterface $engine,
        AttributesFinder $attributeFinder
    ) {
        $this->storage = $storage;
        $this->registry = $registry;
        $this->engine = $engine;
        $this->attributeFinder = $attributeFinder;
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
        $widget = $this->registry->get($data['widget_type'])->getInstance();
        $widget->configure($config);
        $config->merge($data['attributes']);

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
        $widget['attributes'] = $this->attributeFinder->findAll('widget', 'scope', $widget['id']);

        return $widget;
    }
}
