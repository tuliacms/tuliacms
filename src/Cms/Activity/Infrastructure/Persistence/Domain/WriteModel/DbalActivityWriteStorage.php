<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Activity\Ports\Infrastructure\Persistence\Domain\WriteModel\ActivityWriteStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalActivityWriteStorage implements ActivityWriteStorageInterface
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $row): void
    {
        if ($this->recordExists($row['id'])) {
            $this->connection->update('#__activity', [
                'message'            => $row['message'],
                'context'            => $row['context'],
                'translation_domain' => $row['translationDomain'],
            ], [
                'id' => $row['id'],
            ]);
        } else {
            $this->connection->insert('#__activity', [
                'id'                 => $row['id'],
                'website_id'         => $row['websiteId'],
                'message'            => $row['message'],
                'context'            => $row['context'],
                'translation_domain' => $row['translationDomain'],
                'created_at'         => $row['createdAt'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $id): void
    {
        $this->connection->delete('#__activity', ['id' => $id]);
    }

    private function recordExists(string $id): bool
    {
        return $id === $this->connection->fetchFirstColumn('SELECT id FROM #__activity WHERE id = :id LIMIT 1', ['id' => $id]);
    }
}
