<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\WriteModel;

use PDO;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\ChildrenTermsLevelStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalChildrenTermsLevelStorage implements ChildrenTermsLevelStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function findChildren(array $idList): array
    {
        $source = $this->connection->fetchAllAssociative('SELECT id, `level`, parent_id FROM #__term WHERE parent_id = :parent_id', [
            'parent_id' => $idList,
        ], [
            'parent_id' => ConnectionInterface::PARAM_ARRAY_STR,
        ]);

        $result = [];

        foreach ($source as $row) {
            $result[$row['parent_id']][$row['id']] = (int) $row['level'];
        }

        return $result;
    }

    public function updateChildrenLevels(array $levels): void
    {
        foreach ($levels as $id => $level) {
            $this->connection->update(
                '#__term',
                ['level' => $level],
                ['id' => $id],
                ['level' => PDO::PARAM_INT]
            );
        }
    }
}
