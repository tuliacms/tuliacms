<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Persistence;

use Tulia\Cms\Activity\Model\ActivityRow;
use Tulia\Cms\Activity\Model\ActivityStorageInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalActivityStorage implements ActivityStorageInterface
{
    private ConnectionInterface $connection;
    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(ConnectionInterface $connection, CurrentWebsiteInterface $currentWebsite)
    {
        $this->connection = $connection;
        $this->currentWebsite = $currentWebsite;
    }

    public function store(ActivityRow $activityRow): void
    {
        if ($this->recordExists($activityRow->getId())) {
            $this->connection->update('#__activity', [
                'message'            => $activityRow->getMessage(),
                'context'            => json_encode($activityRow->getContext()),
                'translation_domain' => $activityRow->getTranslationDomain(),
            ], [
                'id' => $activityRow->getId(),
            ]);
        } else {
            $this->connection->insert('#__activity', [
                'id'                 => $activityRow->getId(),
                'website_id'         => $activityRow->getWebsiteId(),
                'message'            => $activityRow->getMessage(),
                'context'            => json_encode($activityRow->getContext()),
                'translation_domain' => $activityRow->getTranslationDomain(),
                'created_at'         => $activityRow->getCreatedAt(),
            ]);
        }
    }

    public function delete(ActivityRow $activityRow): void
    {
        $this->connection->delete('#__activity', ['id' => $activityRow->getId()]);
    }


    public function findCollection(int $start = 0, int $limit = 10): array
    {
        $source = $this->connection->fetchAllAssociative(
            "SELECT * FROM #__activity WHERE website_id = :website_id ORDER BY created_at DESC LIMIT {$start}, {$limit}",
            ['website_id' => $this->currentWebsite->getId()]
        );

        foreach ($source as $row) {
            $row['context'] = json_decode($row['context'], true);
            $result[] = ActivityRow::fromArray($row);
        }

        return $result;
    }


    private function recordExists(string $id): bool
    {
        return $id === $this->connection->fetchFirstColumn('SELECT id FROM #__activity WHERE id = :id LIMIT 1', ['id' => $id]);
    }
}
