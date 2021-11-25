<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\ReadModel;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements NodeFinderInterface
{
    private ConnectionInterface $connection;

    private MetadataFinder $metadataFinder;
    private NodeTypeRegistry $nodeTypeRegistry;

    public function __construct(
        ConnectionInterface $connection,
        MetadataFinder $metadataFinder,
        NodeTypeRegistry $nodeTypeRegistry
    ) {
        $this->connection = $connection;
        $this->metadataFinder = $metadataFinder;
        $this->nodeTypeRegistry = $nodeTypeRegistry;
    }

    public function getAlias(): string
    {
        return 'node';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalFinderQuery($this->connection->createQueryBuilder(), $this->metadataFinder, $this->nodeTypeRegistry);
    }
}
