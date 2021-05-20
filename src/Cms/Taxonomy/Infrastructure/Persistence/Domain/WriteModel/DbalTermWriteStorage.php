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
        return $this->prepareQueryBuilder($locale, $defaultLocale)
            ->andWhere('tm.type = :type')
            ->addOrderBy('tm.global_order', 'ASC')
            ->setParameter('type', $type)
            ->execute()
            ->fetchAllAssociative();
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
        $mainTable['global_order'] = $data['global_order'];

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
        $mainTable['global_order'] = $data['global_order'];

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

    private function prepareQueryBuilder(string $locale, string $defaultLocale): QueryBuilder
    {
        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.name), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        return $this->connection->createQueryBuilder()
            ->select("tm.*,
                tp.path,
                COALESCE(tl.name, tm.name) AS name,
                COALESCE(tl.slug, tm.slug) AS slug,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.locale, :locale) AS locale,
                {$translationColumn}")
            ->from('#__term', 'tm')
            ->leftJoin('tm', '#__term_lang', 'tl', 'tm.id = tl.term_id AND tl.locale = :locale')
            ->leftJoin('tm', '#__term_path', 'tp', 'tp.term_id = tm.id AND tl.locale = :locale')
            ->setParameters([
                'locale' => $locale
            ]);
    }
}
