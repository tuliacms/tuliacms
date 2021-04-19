<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Finder;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDatatableFinder implements FinderInterface
{
    /**
     * @var ConnectionInterface|Connection
     */
    protected Connection $connection;
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(Connection $connection, CurrentWebsiteInterface $currentWebsite)
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
}
