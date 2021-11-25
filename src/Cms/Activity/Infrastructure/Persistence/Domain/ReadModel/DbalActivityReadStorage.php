<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Infrastructure\Persistence\Domain\ReadModel;

use Tulia\Cms\Activity\Ports\Infrastructure\Persistence\Domain\ReadModel\ActivityReadStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalActivityReadStorage implements ActivityReadStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function findCollection(array $criteria, int $start = 0, int $limit = 10): array
    {
        $where = '';
        $binds = [];

        if (empty($criteria) === false) {
            $parts = [];

            foreach ($criteria as $col => $val) {
                $parts[] = "{$col} = :{$col}";
                $binds[$col] = $val;
            }

            $where = 'WHERE ' . implode(' AND ', $parts);
        }

        return $this->connection->fetchAllAssociative("SELECT * FROM #__activity {$where} ORDER BY created_at DESC LIMIT {$start}, {$limit}", $binds);
    }
}
