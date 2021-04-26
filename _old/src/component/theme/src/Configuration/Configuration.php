<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * {@inheritdoc}
     */
    public function getRegisteredWidgetSpaces(): array
    {
        if (isset($this->configuration['widget_space']) === false) {
            return [];
        }

        $result = [];

        foreach ($this->configuration['widget_space'] as $name => $data) {
            $space = [
                'name' => $name,
                'label' => $name,
                'translation_domain' => null,
            ];

            if (is_array($data)) {
                $space = array_merge($space, $data);
            } else {
                $space['label'] = $data;
            }

            $result[] = $space;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisteredWidgetStyles(): array
    {
        if (isset($this->configuration['widget_style']) === false) {
            return [];
        }

        $result = [];

        foreach ($this->configuration['widget_style'] as $name => $data) {
            $space = [
                'name' => $name,
                'label' => $name,
                'translation_domain' => null,
            ];

            if (is_array($data)) {
                $space = array_merge($space, $data);
            } else {
                $space['label'] = $data;
            }

            $result[] = $space;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $group, string $id, $value = null): void
    {
        $this->configuration[$group][$id] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function all(?string $group = null): array
    {
        if ($group === null) {
            return $this->configuration;
        }

        return $this->configuration[$group] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $group, string $id, $default = null)
    {
        return $this->configuration[$group][$id] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $group, ?string $id = null): void
    {
        if ($id !== null) {
            unset($this->configuration[$group][$id]);
        } else {
            unset($this->configuration[$group]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $group, ?string $id = null, ?string $valueKey = null): bool
    {
        if ($group && $id && $valueKey) {
            return isset($this->configuration[$group][$id][$valueKey]);
        }
        if ($group && $id && ! $valueKey) {
            return isset($this->configuration[$group][$id]);
        }

        return isset($this->configuration[$group]);
    }
}
