<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\ReadModel\Finder;

use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\ReadModel\MenuFinderInterface;
use Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\DbalQuery;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Finder extends AbstractFinder implements MenuFinderInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getAlias(): string
    {
        return 'menu';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder());
    }
}