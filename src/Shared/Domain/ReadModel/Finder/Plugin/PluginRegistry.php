<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\ReadModel\Finder\Plugin;

use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin\PluginInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PluginRegistry
{
    protected array $plugins = [];

    public function addPlugin(PluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    /**
     * @param string $storage
     * @return PluginInterface[]
     */
    public function getSupportedPlugins(string $storage): iterable
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin->supportsStorage($storage)) {
                yield $plugin;
            }
        }
    }
}
