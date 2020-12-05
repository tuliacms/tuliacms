<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Plugin;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
interface PluginInterface
{
    public function supports(string $configurationKey): bool;
    public function getColumns(): array;
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder;
    public function buildActions(array $row): array;
    public function getFilters(): array;
    public function prepareResult(array $result): array;
}
