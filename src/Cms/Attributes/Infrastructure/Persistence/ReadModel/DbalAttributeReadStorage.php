<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Infrastructure\Persistence\ReadModel;

use Tulia\Cms\Attributes\Domain\ReadModel\Service\AttributeReadStorageInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalAttributeReadStorage implements AttributeReadStorageInterface
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
            tm.uri,
            tm.name,
            tm.is_renderable,
            tm.has_nonscalar_value,
            COALESCE(tl.value, tm.value) AS `value`
        FROM #__{$type}_attribute AS tm
        LEFT JOIN #__{$type}_attribute_lang AS tl
            ON tm.id = tl.attribute_id AND tl.locale = :locale
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
            $result[$row['owner_id']][$row['uri']] = [
                'value' => $row['value'],
                'uri' => $row['uri'],
                'name' => $row['name'],
                'is_renderable' => $row['is_renderable'],
                'has_nonscalar_value' => $row['has_nonscalar_value'],
            ];
        }

        return $result;
    }
}
