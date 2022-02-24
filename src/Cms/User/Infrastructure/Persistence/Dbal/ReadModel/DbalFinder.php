<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Dbal\ReadModel;

use Tulia\Cms\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements UserFinderInterface
{
    private ConnectionInterface $connection;
    private AttributesFinder $metadataFinder;

    public function __construct(
        ConnectionInterface $connection,
        AttributesFinder $metadataFinder
    ) {
        $this->connection = $connection;
        $this->metadataFinder = $metadataFinder;
    }

    public function getAlias(): string
    {
        return 'user';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalFinderQuery(
            $this->connection->createQueryBuilder(),
            $this->metadataFinder
        );
    }
}
