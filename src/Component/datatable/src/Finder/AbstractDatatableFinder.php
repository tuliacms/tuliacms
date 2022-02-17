<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Finder;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDatatableFinder implements FinderInterface
{
    protected ConnectionInterface $connection;

    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(ConnectionInterface $connection, CurrentWebsiteInterface $currentWebsite)
    {
        $this->connection = $connection;
        $this->currentWebsite = $currentWebsite;
    }

    abstract public function getConfigurationKey(): string;
    abstract public function getColumns(): array;
    abstract public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder;

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    public function buildActions(array $row): array
    {
        return [];
    }

    public function getFilters(): array
    {
        return [];
    }

    public function prepareResult(array $result): array
    {
        return $result;
    }

    public function fetchAllAssociative(QueryBuilder $queryBuilder): array
    {
        return $queryBuilder->execute()->fetchAllAssociative();
    }
}
