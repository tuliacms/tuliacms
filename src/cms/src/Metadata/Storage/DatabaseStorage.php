<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Storage;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorage implements StorageInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $database;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuid;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param ConnectionInterface $database
     * @param UuidGeneratorInterface $uuid
     * @param CurrentWebsiteInterface $currentWebsite
     */
    public function __construct(
        ConnectionInterface $database,
        UuidGeneratorInterface $uuid,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->database       = $database;
        $this->uuid           = $uuid;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function getMany(string $type, $elementId, array $names): array
    {
        return $this->query($type, $elementId, $names);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $type, $elementId, string $name, $value, bool $multilingual = false): void
    {
        $mainRow = $this->database->fetchAll("SELECT * FROM #__{$type}_metadata AS tm
            WHERE (
                {$type}_id = :element_id
                AND name = :name
            )", [
            ':name'       => $name,
            ':element_id' => $elementId,
        ]);

        if (! $mainRow) {
            if (empty($value)) {
                // Don't create metadata if empty value.
                return;
            }

            $this->insert($type, $elementId, $name, $value, $multilingual);
            return;
        }

        $mainRow = $mainRow[0];

        // If metadata exists, and new value is empty,
        // remove this metadata to free-up storage.
        if (empty($value)) {
            $this->delete($type, $elementId, $name);
            return;
        }

        if ($multilingual) {
            $criteria = [
                'metadata_id' => $mainRow['id'],
                'locale'      => $this->getCurrentLocale(),
            ];
        } else {
            $criteria = [
                'metadata_id' => $mainRow['id'],
            ];
        }

        $this->database->update("#__{$type}_metadata_lang", ['value' => $value], $criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function insert(string $type, $elementId, string $name, $value, bool $multilingual = false): void
    {
        $id = $this->uuid->generate();

        $this->database->insert("#__{$type}_metadata", [
            'id'   => $id,
            'name' => $name,
            "{$type}_id" => $elementId,
        ]);

        foreach ($this->currentWebsite->getLocales() as $locale) {
            $this->database->insert("#__{$type}_metadata_lang", [
                'metadata_id' => $id,
                'locale'      => $locale->getCode(),
                'value'       => $value,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $type, $elementId, string $name): void
    {
        $source = $this->database->fetchAll("SELECT tm.id FROM #__{$type}_metadata AS tm
            INNER JOIN #__{$type}_metadata_lang AS tl
            ON (
                tm.id = tl.metadata_id
            )
            WHERE (
                tm.{$type}_id = :element_id
                AND tm.name = :name
            )", [
            ':element_id' => $elementId,
            ':name'       => $name
        ]);

        foreach ($source as $row) {
            $this->database->delete("#__{$type}_metadata", [
                'id' => $row['id'],
            ]);

            $this->database->delete("#__{$type}_metadata_lang", [
                'metadata_id' => $row['id'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll(string $type, $elementId): void
    {
        $source = $this->database->fetchAll("SELECT tm.id FROM #__{$type}_metadata AS tm
            INNER JOIN #__{$type}_metadata_lang AS tl
            ON (
                tm.id = tl.metadata_id
            )
            WHERE (
                tm.{$type}_id = :element_id
            )", [
            ':element_id' => $elementId
        ]);

        foreach ($source as $row) {
            $this->database->delete("#__{$type}_metadata", [
                'id' => $row['id'],
            ]);

            $this->database->delete("#__{$type}_metadata_lang", [
                'metadata_id' => $row['id'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function query(string $type, $elementId, array $names): array
    {
        $count = count($names);

        $sql = "SELECT * FROM #__{$type}_metadata AS tm
        INNER JOIN #__{$type}_metadata_lang AS tl
            ON tm.id = tl.metadata_id
        WHERE
            1 = 1
            AND tm.{$type}_id = :element_id
            AND tm.name IN (:names)
            AND tl.locale = :locale
        LIMIT {$count}";

        $source = $this->database->fetchAll($sql, [
            ':locale'     => $this->getCurrentLocale(),
            ':element_id' => $elementId,
            ':names'      => $names,
        ], [
            ':names'      => ConnectionInterface::PARAM_ARRAY_STR,
        ]);

        $result = [];

        foreach ($source as $row) {
            $result[$row['name']] = $row['value'];
        }

        foreach ($names as $name) {
            if (isset($result[$name]) === false) {
                $result[$name] = null;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getCurrentLocale(): string
    {
        return $this->currentWebsite->getLocale()->getCode();
    }
}
