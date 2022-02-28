<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
class ConfigurationRegistry
{
    private array $configurations = [];

    public function addConfiguration(string $theme, string $group, ConfigurationInterface $configuration): void
    {
        $this->configurations[$theme][$group] = $configuration;
    }

    public function get(string $theme, string $group = 'base'): ConfigurationInterface
    {
        return $this->configurations[$theme][$group] ?? new Configuration();
    }
}
