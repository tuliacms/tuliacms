<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\ReadModel;

use Tulia\Cms\Node\Domain\ReadModel\Persistence\FlagsPersistenceInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFlagsPersistence implements FlagsPersistenceInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function update(string $nodeId, array $flags): void
    {
        $this->connection->beginTransaction();

        try {
            $this->connection->delete('#__node_has_flag', ['node_id' => $nodeId]);

            foreach ($flags as $flag) {
                $this->connection->insert('#__node_has_flag', [
                    'node_id' => $nodeId,
                    'flag' => $flag,
                ]);
            }

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
