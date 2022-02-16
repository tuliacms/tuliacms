<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\ReadModel;

use Tulia\Cms\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements NodeFinderInterface
{
    private ConnectionInterface $connection;
    private AttributesFinder $metadataFinder;
    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        ConnectionInterface $connection,
        AttributesFinder $metadataFinder,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->connection = $connection;
        $this->metadataFinder = $metadataFinder;
        $this->currentWebsite = $currentWebsite;
    }

    public function getAlias(): string
    {
        return 'node';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalFinderQuery(
            $this->connection->createQueryBuilder(),
            $this->metadataFinder,
            $this->currentWebsite
        );
    }
}
