<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\TermWriteStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTermWriteStorage extends AbstractLocalizableStorage implements TermWriteStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $type, string $locale, string $defaultLocale): array
    {
        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.name), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        return $this->connection->fetchAll("
            SELECT
                tm.*,
                COALESCE(tl.name, tm.name) AS name,
                COALESCE(tl.slug, tm.slug) AS slug,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.locale, :locale) AS locale,
                {$translationColumn}
            FROM #__term AS tm
            LEFT JOIN #__term_lang AS tl
                ON tm.id = tl.term_id AND tl.locale = :locale
            WHERE tm.type = :type", [
            'type' => $type,
            'locale' => $locale
        ]);
    }

    public function delete(array $term): void
    {
        $this->connection->delete('#__term', ['id' => $term['id']]);
        $this->connection->delete('#__term_lang', ['term_id' => $term['id']]);
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

        $this->connection->insert('#__term', $mainTable);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['website_id'] = $data['website_id'];
        $mainTable['type'] = $data['type'];
        $mainTable['parent_id'] = $data['parent_id'];
        $mainTable['level'] = $data['level'];

        if ($foreignLocale === false) {
            $mainTable['name'] = $data['name'];
            $mainTable['slug'] = $data['slug'];
            $mainTable['visibility'] = $data['visibility'] ? '1' : '0';
        }

        $this->connection->update('#__term', $mainTable, ['id' => $data['id']]);
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
}
