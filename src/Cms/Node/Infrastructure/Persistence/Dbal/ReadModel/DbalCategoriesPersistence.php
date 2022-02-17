<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\ReadModel;

use Tulia\Cms\Node\Domain\ReadModel\Persistence\CategoriesPersistenceInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalCategoriesPersistence implements CategoriesPersistenceInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function update(string $nodeId, string $taxonomy, array $categories): void
    {
        $this->connection->beginTransaction();

        try {
            $this->connection->delete('#__node_term_relationship', [
                'node_id'  => $nodeId,
                'taxonomy' => $taxonomy,
            ]);

            foreach ($categories as $termId => $type) {
                $this->connection->insert('#__node_term_relationship', [
                    'node_id'  => $nodeId,
                    'term_id'  => $termId,
                    'taxonomy' => $taxonomy,
                    'type'     => $type,
                ]);
            }

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
