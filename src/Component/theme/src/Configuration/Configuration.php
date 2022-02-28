<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration implements ConfigurationInterface
{
    protected array $configuration = [];

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

    public function merge(ConfigurationInterface $configuration): void
    {
        $this->configuration = array_merge_recursive($this->configuration, $configuration->all());
    }

    public function add(string $group, string $code, $value = null): void
    {
        $this->configuration[$group][$code] = $value;
    }

    public function all(?string $group = null): array
    {
        if ($group === null) {
            return $this->configuration;
        }

        return $this->configuration[$group] ?? [];
    }

    public function get(string $group, string $code, $default = null)
    {
        return $this->configuration[$group][$code] ?? $default;
    }

    public function remove(string $group, ?string $code = null): void
    {
        if ($code !== null) {
            unset($this->configuration[$group][$code]);
        } else {
            unset($this->configuration[$group]);
        }
    }

    public function has(string $group, ?string $code = null, ?string $valueKey = null): bool
    {
        if ($group && $code && $valueKey) {
            return isset($this->configuration[$group][$code][$valueKey]);
        }
        if ($group && $code && ! $valueKey) {
            return isset($this->configuration[$group][$code]);
        }

        return isset($this->configuration[$group]);
    }
}
