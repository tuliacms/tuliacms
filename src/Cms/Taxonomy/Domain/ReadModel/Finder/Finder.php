<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Finder;

use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\DbalQuery;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Finder extends AbstractFinder implements TermFinderInterface
{
    private ConnectionInterface $connection;

    private MetadataFinder $metadataFinder;

    public function __construct(ConnectionInterface $connection, MetadataFinder $metadataFinder)
    {
        $this->connection = $connection;
        $this->metadataFinder = $metadataFinder;
    }

    public function getAlias(): string
    {
        return 'term';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder(), $this->metadataFinder);
    }
}
