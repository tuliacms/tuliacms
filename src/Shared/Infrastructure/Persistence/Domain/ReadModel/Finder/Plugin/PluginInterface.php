<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin;

/**
 * @author Adam Banaszkiewicz
 */
interface PluginInterface
{
    public function supportsStorage(string $storage): bool;
    public function filterCriteria(array $criteria): array;
}
