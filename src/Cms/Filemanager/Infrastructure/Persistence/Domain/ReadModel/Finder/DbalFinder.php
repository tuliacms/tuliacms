<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Persistence\Domain\ReadModel\Finder;

use Tulia\Cms\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements FileFinderInterface
{
    private ConnectionInterface $connection;

    private AttributesFinder $metadataFinder;

    public function __construct(ConnectionInterface $connection, AttributesFinder $metadataFinder)
    {
        $this->connection = $connection;
        $this->metadataFinder = $metadataFinder;
    }

    public function getAlias(): string
    {
        return 'file';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder(), $this->metadataFinder);
    }
}
