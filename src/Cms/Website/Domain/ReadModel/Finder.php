<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\QueryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Website\Infrastructure\Persistence\Domain\ReadModel\DbalQuery;

/**
 * @author Adam Banaszkiewicz
 */
class Finder extends AbstractFinder
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getAlias(): string
    {
        return 'website';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder());
    }
}