<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\ReadModel\Finder;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\DbalQuery;

/**
 * @author Adam Banaszkiewicz
 */
class Finder extends AbstractFinder implements WidgetFinderInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getAlias(): string
    {
        return 'widget';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder());
    }
}
