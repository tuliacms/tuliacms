<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Plugin;

use Tulia\Component\Datatable\Finder\FinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PluginsRegistry
{
    /**
     * @var PluginInterface[]
     */
    private iterable $plugins;

    public function __construct(iterable $plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * @param FinderInterface $finder
     *
     * @return PluginInterface[]
     */
    public function getForFinder(FinderInterface $finder): array
    {
        $plugins = [];

        foreach ($this->plugins as $plugin) {
            if ($plugin->supports($finder->getConfigurationKey())) {
                $plugins[] = $plugin;
            }
        }

        return $plugins;
    }
}
