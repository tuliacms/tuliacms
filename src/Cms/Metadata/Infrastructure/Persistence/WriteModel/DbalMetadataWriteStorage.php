<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Infrastructure\Persistence\WriteModel;

use Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\WriteModel\MetadataWriteStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMetadataWriteStorage extends AbstractLocalizableStorage implements MetadataWriteStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $type, array $ownerIdList, array $attributes, string $locale): array
    {
        $sql = "SELECT
            tm.owner_id,
            tm.name,
            tm.uri,
            COALESCE(tl.value, tm.value) AS `value`
        FROM #__{$type}_metadata AS tm
        LEFT JOIN #__{$type}_metadata_lang AS tl
            ON tm.id = tl.metadata_id AND tl.locale = :locale
        WHERE
            tm.owner_id IN (:owner_id)
            AND tm.name IN (:names)";

        $source = $this->connection->fetchAllAssociative($sql, [
            'locale' => $locale,
            'owner_id' => $ownerIdList,
            'names' => $attributes,
        ], [
            'owner_id' => ConnectionInterface::PARAM_ARRAY_STR,
            'names' => ConnectionInterface::PARAM_ARRAY_STR,
        ]);

        $result = [];

        foreach ($source as $row) {
            $result[$row['owner_id']][$row['uri']] = [
                'value' => $row['value'],
                'uri' => $row['uri'],
                'name' => $row['name'],
            ];
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

    public function delete(string $type, string $ownerId): void
    {
        $this->connection->executeUpdate("DELETE tm, tl
            FROM #__{$type}_metadata AS tm
            JOIN #__{$type}_metadata_lang AS tl
                ON tm.id = tl.metadata_id
            WHERE
                tm.owner_id = :owner_id", [
            'owner_id' => $ownerId,
        ]);
    }

    protected function insertMainRow(array $data): void
    {
        $this->connection->insert("#__{$data['type']}_metadata", [
            'id' => $data['id'],
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'value' => $data['value'],
            'uri' => $data['uri'],
        ]);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        if ($foreignLocale && $data['multilingual'] === true) {
            return;
        }

        $this->connection->update("#__{$data['type']}_metadata", [
            'value' => $data['value'],
        ], [
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'uri' => $data['uri'],
        ]);
    }

    protected function insertLangRow(array $data): void
    {
        if ($data['multilingual'] === false) {
            return;
        }

        $this->connection->insert("#__{$data['type']}_metadata_lang", [
            'metadata_id' => $data['id'],
            'value' => $data['value'],
            'locale' => $data['locale'],
        ]);
    }

    protected function updateLangRow(array $data): void
    {
        if ($data['multilingual'] === false) {
            return;
        }

        $this->connection->update("#__{$data['type']}_metadata_lang", [
            'value' => $data['value'],
        ], [
            'metadata_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        if ($data['multilingual'] === false) {
            return false;
        }

        $sql = "SELECT
            tl.metadata_id
        FROM #__{$data['type']}_metadata AS tm
        LEFT JOIN #__{$data['type']}_metadata_lang AS tl
            ON tm.id = tl.metadata_id AND tl.locale = :locale
        WHERE
            tm.owner_id IN (:owner_id) AND tm.`uri` = :uri
        LIMIT 1";

        $result = $this->connection->fetchAllAssociative($sql, [
            'locale' => $data['locale'],
            'owner_id' => $data['owner_id'],
            'uri' => $data['uri'],
        ], [
            'owner_id' => \PDO::PARAM_STR,
        ]);

        return isset($result[0]['metadata_id']) && $result[0]['metadata_id'] === $data['id'];
    }

    protected function findMainRow(array $metadata): array
    {
        $sql = "SELECT id
        FROM #__{$metadata['type']}_metadata
        WHERE owner_id = :owner_id AND `uri` = :uri
        LIMIT 1";

        $result = $this->connection->fetchAllAssociative($sql, [
            'uri' => $metadata['uri'],
            'owner_id' => $metadata['owner_id'],
        ], [
            'uri' => \PDO::PARAM_STR,
            'owner_id' => \PDO::PARAM_STR,
        ]);

        return $result[0] ?? [];
    }
}
