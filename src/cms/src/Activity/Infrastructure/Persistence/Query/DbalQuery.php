<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Infrastructure\Persistence\Query;

use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery implements QueryInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @param ConnectionInterface $connection
     */
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
                $binds[":{$col}"] = $val;
            }

            $where = 'WHERE ' . implode(' AND ', $parts);
        }

        return $this->connection->fetchAll("SELECT * FROM #__activity {$where} ORDER BY created_at DESC LIMIT {$start}, {$limit}", $binds);
    }
}
