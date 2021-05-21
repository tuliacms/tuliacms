<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Finder;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
interface FinderInterface
{
    public function getConfigurationKey(): string;

    public function getColumns(): array;

    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder;

    public function getQueryBuilder(): QueryBuilder;

    public function buildActions(array $row): array;

    public function getFilters(): array;

    public function prepareResult(array $result): array;

    public function fetchAllAssociative(QueryBuilder $queryBuilder): array;
}
