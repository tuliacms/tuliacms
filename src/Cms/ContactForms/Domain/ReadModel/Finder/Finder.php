<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\ReadModel\Finder;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\DbalQuery;
use Tulia\Cms\ContactForms\Ports\Domain\ReadModel\ContactFormFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Finder extends AbstractFinder implements ContactFormFinderInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getAlias(): string
    {
        return 'contact_forms';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder());
    }
}
