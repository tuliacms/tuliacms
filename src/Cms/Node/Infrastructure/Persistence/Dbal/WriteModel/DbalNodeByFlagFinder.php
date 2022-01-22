<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\WriteModel;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByFlagFinderInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeByFlagFinder implements NodeByFlagFinderInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function findOtherNodesWithFlags(string $localNode, array $flags, string $websiteId): array
    {
        return $this->connection->fetchAllAssociative('SELECT tm.id, tnhf.flag
            FROM #__node AS tm
            INNER JOIN #__node_has_flag AS tnhf
                ON tm.id = tnhf.node_id AND tnhf.flag IN (:flags)
            WHERE
                tm.id != :nodeId AND tm.website_id = :websiteId', [
            'flags' => $flags,
            'nodeId' => $localNode,
            'websiteId' => $websiteId,
        ], [
            'flags' => ConnectionInterface::PARAM_ARRAY_STR,
            'nodeId' => ConnectionInterface::PARAM_STR,
            'websiteId' => ConnectionInterface::PARAM_STR,
        ]);
    }
}
