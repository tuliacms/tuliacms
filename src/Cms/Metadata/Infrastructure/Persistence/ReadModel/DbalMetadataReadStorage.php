<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Infrastructure\Persistence\ReadModel;

use Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\ReadModel\MetadataReadStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMetadataReadStorage implements MetadataReadStorageInterface
{
    protected ConnectionInterface $connection;

    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(ConnectionInterface $connection, CurrentWebsiteInterface $currentWebsite)
    {
        $this->connection = $connection;
        $this->currentWebsite = $currentWebsite;
    }

    public function findAll(string $type, array $ownerId): array
    {
        $sql = "SELECT
            tm.owner_id,
            tm.name,
            COALESCE(tl.value, tm.value) AS `value`
        FROM #__{$type}_metadata AS tm
        LEFT JOIN #__{$type}_metadata_lang AS tl
            ON tm.id = tl.metadata_id AND tl.locale = :locale
        WHERE
            tm.owner_id IN (:owner_id)";

        $source = $this->connection->fetchAllAssociative($sql, [
            'locale' => $this->currentWebsite->getLocale()->getCode(),
            'owner_id' => $ownerId,
        ], [
            'owner_id' => ConnectionInterface::PARAM_ARRAY_STR,
        ]);

        $result = [];

        foreach ($source as $row) {
            $result[$row['owner_id']][$row['name']] = $row['value'];
        }

        return $result;
    }
}
