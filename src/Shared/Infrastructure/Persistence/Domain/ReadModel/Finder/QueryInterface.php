<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
interface QueryInterface
{
    public function getSupportedStorage(): string;
    public function getBaseQueryArray(): array;
    public function query(array $query, array $parameters = []): Collection;
    public function countFoundRows(): int;
    public function setPlugins(array $plugins): void;
}
