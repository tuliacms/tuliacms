<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Infrastructure\Persistence\WriteModel;

use Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\WriteModel\MetadataStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMetadataStorage extends AbstractLocalizableStorage implements MetadataStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $type, array $ownerIdList, string $locale): array
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

        $source = $this->connection->fetchAll($sql, [
            'locale' => $locale,
            'owner_id' => $ownerIdList,
        ], [
            'owner_id' => ConnectionInterface::PARAM_ARRAY_STR,
        ]);

        $result = [];

        foreach ($source as $row) {
            $result[$row['owner_id']][$row['name']] = $row['value'];
        }

        return $result;
    }

    public function persist(array $metadata, string $defaultLocale): void
    {
        foreach ($metadata as $item) {
            $mainRow = $this->findMainRow($item);

            if ($mainRow === []) {
                $this->insert($item, $defaultLocale);
            } else {
                $item['id'] = $mainRow['id'];
                $this->update($item, $defaultLocale);
            }
        }
    }

    protected function insertMainRow(array $data): void
    {
        $this->connection->insert("#__{$data['type']}_metadata", [
            'id' => $data['id'],
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'value' => $data['value'],
        ]);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        if ($foreignLocale) {
            return;
        }

        $this->connection->update("#__{$data['type']}_metadata", [
            'value' => $data['value'],
        ], [
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
        ]);
    }

    protected function insertLangRow(array $data): void
    {
        $this->connection->insert("#__{$data['type']}_metadata_lang", [
            'metadata_id' => $data['id'],
            'value' => $data['value'],
            'locale' => $data['locale'],
        ]);
    }

    protected function updateLangRow(array $data): void
    {
        $this->connection->update("#__{$data['type']}_metadata_lang", [
            'value' => $data['value'],
        ], [
            'metadata_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        $sql = "SELECT
            tl.metadata_id
        FROM #__{$data['type']}_metadata AS tm
        LEFT JOIN #__{$data['type']}_metadata_lang AS tl
            ON tm.id = tl.metadata_id AND tl.locale = :locale
        WHERE
            tm.owner_id IN (:owner_id) AND tm.`name` = :name
        LIMIT 1";

        $result = $this->connection->fetchAll($sql, [
            'locale' => $data['locale'],
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
        ], [
            'owner_id' => \PDO::PARAM_STR,
        ]);

        return isset($result[0]['metadata_id']) && $result[0]['metadata_id'] === $data['id'];
    }

    protected function findMainRow(array $metadata): array
    {
        $sql = "SELECT id
        FROM #__{$metadata['type']}_metadata
        WHERE owner_id = :owner_id AND `name` = :name
        LIMIT 1";

        $result = $this->connection->fetchAllAssociative($sql, [
            'name' => $metadata['name'],
            'owner_id' => $metadata['owner_id'],
        ], [
            'name' => \PDO::PARAM_STR,
            'owner_id' => \PDO::PARAM_STR,
        ]);

        return $result[0] ?? [];
    }
}
