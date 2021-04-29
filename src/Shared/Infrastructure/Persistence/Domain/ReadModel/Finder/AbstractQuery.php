<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractQuery implements QueryInterface
{
    protected array $plugins = [];

    abstract public function getBaseQueryArray(): array;
    abstract public function query(array $query, array $parameters = []): Collection;
    abstract public function countFoundRows(): int;
    abstract public function getSupportedStorage(): string;

    public function setPlugins(array $plugins): void
    {
        $this->plugins = $plugins;
    }
}
