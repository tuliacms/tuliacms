<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel\Finder;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Website\Infrastructure\Persistence\Domain\WriteModel\ReadModel\Finder\Query\DbalQuery;
use Tulia\Cms\Website\Ports\Infrastructure\Persistence\Domain\ReadModel\WebsiteFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Finder extends AbstractFinder implements WebsiteFinderInterface
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
