<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Plugin\PluginRegistry;

/**
 * @author Adam Banaszkiewicz
 */
interface QueryInterface
{
    public function getSupportedStorage(): string;

    public function getBaseQueryArray(): array;

    public function query(array $criteria): Collection;

    public function countFoundRows(): int;

    public function setPluginsRegistry(PluginRegistry $pluginRegistry): void;

    public function callPlugins(array $criteria): void;
}
