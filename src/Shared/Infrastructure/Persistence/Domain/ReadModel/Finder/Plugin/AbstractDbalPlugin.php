<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin;

use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDbalPlugin implements DbalPluginInterface
{
    public function supportsStorage(string $storage): bool
    {
        return $storage === AbstractDbalQuery::STORAGE_NAME;
    }

    public function filterCriteria(array $criteria): array
    {
        return $criteria;
    }

    public function handle(AbstractDbalQuery $query, array $criteria): void
    {
    }
}
