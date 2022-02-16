<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Plugin\PluginRegistry;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractQuery implements QueryInterface
{
    protected PluginRegistry $pluginRegistry;

    abstract public function getBaseQueryArray(): array;
    abstract public function query(array $criteria, string $scope): Collection;
    abstract public function countFoundRows(): int;
    abstract public function getSupportedStorage(): string;

    public function setPluginsRegistry(PluginRegistry $pluginRegistry): void
    {
        $this->pluginRegistry = $pluginRegistry;
    }

    public function filterCriteria(array $criteria): array
    {
        foreach ($this->pluginRegistry->getSupportedPlugins($this->getSupportedStorage()) as $plugin) {
            $criteria = $plugin->filterCriteria($criteria);
        }

        return $criteria;
    }

    public function callPlugins(array $criteria): void
    {
        foreach ($this->pluginRegistry->getSupportedPlugins($this->getSupportedStorage()) as $plugin) {
            $plugin->handle($this, $criteria);
        }
    }
}
