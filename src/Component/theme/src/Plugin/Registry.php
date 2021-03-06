<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Plugin;

use Tulia\Component\Theme\Customizer\Builder\Plugin\PluginInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class Registry implements RegistryInterface
{
    /**
     * @var PluginInterface[]
     */
    protected $plugins = [];

    public function __construct(iterable $plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins(): iterable
    {
        return $this->plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function callPlugins($method, ...$args): void
    {
        foreach ($this->plugins as $plugin) {
            $plugin->$method(...$args);
        }
    }
}
