<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\TermPathWriteStorageInterface;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\TermWriteStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTermWriteStorage extends AbstractLocalizableStorage implements TermWriteStorageInterface
{
    private ConnectionInterface $connection;

    private TermPathWriteStorageInterface $termPathStorage;

    public function __construct(ConnectionInterface $connection, TermPathWriteStorageInterface $termPathStorage)
    {
        $this->connection = $connection;
        $this->termPathStorage = $termPathStorage;
    }

    public function findByType(string $type, string $locale, string $defaultLocale): array
    {
        return $this->fetchAllAssociative($locale, $defaultLocale, $type);
    }

    public function find(string $id, string $locale, string $defaultLocale): array
    {
        $result = $this->prepareQueryBuilder($locale, $defaultLocale)
            ->andWhere('tm.id = :id')
            ->setMaxResults(1)
            ->setParameter('id', $id)
            ->execute()
            ->fetchAllAssociative();

        return $result[0] ?? [];
    }

    public function delete(array $term): void
    {
        $this->connection->delete('#__term', ['id' => $term['id']]);
        $this->connection->delete('#__term_lang', ['term_id' => $term['id']]);
        $this->connection->delete('#__term_path', ['term_id' => $term['id']]);
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollback();
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['website_id'] = $data['website_id'];
        $mainTable['type'] = $data['type'];
        $mainTable['parent_id'] = $data['parent_id'];
        $mainTable['name'] = $data['name'];
        $mainTable['slug'] = $data['slug'];
        $mainTable['visibility'] = $data['visibility'] ? '1' : '0';
        $mainTable['level'] = $data['level'];
        $mainTable['position'] = $data['position'];
        $mainTable['is_root'] = $data['is_root'] ? '1' : '0';

        $this->connection->insert('#__term', $mainTable);

        $this->persistPath($data['id'], $data['path'], $data['locale']);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['website_id'] = $data['website_id'];
        $mainTable['type'] = $data['type'];
        $mainTable['parent_id'] = $data['parent_id'];
        $mainTable['level'] = $data['level'];
        $mainTable['position'] = $data['position'];

        if ($foreignLocale === false) {
            $mainTable['name'] = $data['name'];
            $mainTable['slug'] = $data['slug'];
            $mainTable['visibility'] = $data['visibility'] ? '1' : '0';
        }

        $this->connection->update('#__term', $mainTable, ['id' => $data['id']]);

        $this->persistPath($data['id'], $data['path'], $data['locale']);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['term_id'] = $data['id'];
        $langTable['locale'] = $data['locale'];
        $langTable['name'] = $data['name'];
        $langTable['slug'] = $data['slug'];
        $langTable['visibility'] = $data['visibility'] ? '1' : '0';

        $this->connection->insert('#__term_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['name'] = $data['name'];
        $langTable['slug'] = $data['slug'];
        $langTable['visibility'] = $data['visibility'] ? '1' : '0';

        $this->connection->update('#__term_lang', $langTable, [
            'term_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT term_id FROM #__term_lang WHERE term_id = :id AND locale = :locale LIMIT 1',
            ['id' => $data['id'], 'locale' => $data['locale']]
        );

        return isset($result[0]['term_id']) && $result[0]['term_id'] === $data['id'];
    }

    private function persistPath(string $termId, ?string $path, string $locale): void
    {
        if ($path === null) {
            $this->termPathStorage->remove($termId, $locale);
        } else {
            $this->termPathStorage->save($termId, $locale, $path);
        }
    }

    private function fetchAllAssociative(string $locale, string $defaultLocale, string $type): array
    {
        $parameters = [
            'locale'  => $locale,
            'defaultLocale' => $defaultLocale,
            'type' => $type,
        ];
        $where = ['1 = 1'];

        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.name), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        $where = implode(' AND ', $where);

        $result = $this->connection->fetchAll("
WITH RECURSIVE tree_path (
    id,
    website_id,
    type,
    is_root,
    parent_id,
    position,
    level,
    count,
    locale,
    name,
    slug,
    visibility,
    translated,
    generated_path
) AS (
        SELECT
            id,
            website_id,
            type,
            is_root,
            parent_id,
            position,
            level,
            count,
            :defaultLocale AS locale,
            name,
            slug,
            visibility,
            1 AS translated,
            CONCAT(name, '/') as generated_path
        FROM #__term
        WHERE
            is_root = 1
            AND type = :type
            AND {$where}
    UNION ALL
        SELECT
            tm.id,
            tm.website_id,
            tm.type,
            tm.is_root,
            tm.parent_id,
            tm.position,
            tm.level,
            tm.count,
            COALESCE(tl.locale, :defaultLocale) AS locale,
            COALESCE(tl.name, tm.name) AS name,
            COALESCE(tl.slug, tm.slug) AS slug,
            COALESCE(tl.visibility, tm.visibility) AS visibility,
            {$translationColumn},
            CONCAT(tp.generated_path, tm.name, '/') AS generated_path
        FROM tree_path AS tp
        INNER JOIN #__term AS tm
            ON tp.id = tm.parent_id
        LEFT JOIN #__term_lang AS tl
            ON tm.id = tl.term_id AND tl.locale = :locale
        WHERE
            tm.type = :type
            AND {$where}
)
SELECT * FROM tree_path
ORDER BY generated_path", $parameters);

        return $this->appendPath($result, $locale);
    }

    private function appendPath(array $result, string $locale): array
    {
        $paths = $this->connection->fetchAllAssociative('SELECT * FROM #__term_path WHERE term_id IN (:ids) AND locale = :locale', [
            'locale' => $locale,
            'ids' => array_column($result, 'id'),
        ], [
            'locale' => \PDO::PARAM_STR,
            'ids' => ConnectionInterface::PARAM_ARRAY_STR,
        ]);

        foreach ($result as $id => $term) {
            $result[$id]['path'] = null;

            foreach ($paths as $path) {
                if ($path['term_id'] === $term['id']) {
                    $result[$id]['path'] = $path['path'];
                }
            }
        }

        return $result;
    }
}
