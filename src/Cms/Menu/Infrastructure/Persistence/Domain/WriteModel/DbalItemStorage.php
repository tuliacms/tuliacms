<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\ItemStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalItemStorage extends AbstractLocalizableStorage implements ItemStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(string $menuId, string $defaultLocale, string $locale): array
    {
        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.name), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        return $this->connection->fetchAllAssociative("
WITH RECURSIVE tree_path (
    id,
    menu_id,
    parent_id,
    position,
    level,
    is_root,
    type,
    identity,
    hash,
    target,
    locale,
    name,
    visibility,
    translated,
    path
) AS (
        SELECT
            id,
            menu_id,
            parent_id,
            position,
            level,
            is_root,
            type,
            identity,
            hash,
            target,
            :defaultLocale AS locale,
            name,
            visibility,
            1 AS translated,
            CONCAT(name, '/') as path
        FROM #__menu_item
        WHERE
            is_root = 1
            AND menu_id = :menu_id
    UNION ALL
        SELECT
            tm.id,
            tm.menu_id,
            tm.parent_id,
            tm.position,
            tm.level,
            tm.is_root,
            tm.type,
            tm.identity,
            tm.hash,
            tm.target,
            COALESCE(tl.locale, :defaultLocale) AS locale,
            COALESCE(tl.name, tm.name) AS name,
            COALESCE(tl.visibility, tm.visibility) AS visibility,
            {$translationColumn},
            CONCAT(tp.path, tm.name, '/') AS path
        FROM tree_path AS tp
        INNER JOIN #__menu_item AS tm
            ON tp.id = tm.parent_id
        LEFT JOIN #__menu_item_lang AS tl
            ON tm.id = tl.menu_item_id AND tl.locale = :locale
        WHERE tm.menu_id = :menu_id
)
SELECT * FROM tree_path
ORDER BY path", [
            'menu_id' => $menuId,
            'locale'  => $locale,
            'defaultLocale' => $defaultLocale,
        ]);
    }

    public function delete(string $id): void
    {
        $this->connection->delete('#__menu_item', ['id' => $id]);
        $this->connection->delete('#__menu_item_lang', ['menu_item_id' => $id]);
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['parent_id'] = $data['parent_id'];
        $mainTable['level'] = (int) $data['level'];
        $mainTable['position'] = $data['position'];
        $mainTable['is_root'] = $data['is_root'] ? '1' : '0';
        $mainTable['menu_id'] = $data['menu'];
        $mainTable['type'] = $data['type'];
        $mainTable['identity'] = $data['identity'];
        $mainTable['hash'] = $data['hash'];
        $mainTable['target'] = $data['target'];
        $mainTable['name'] = $data['name'];
        $mainTable['visibility'] = $data['visibility'] ? '1' : '0';

        $this->connection->insert('#__menu_item', $mainTable);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['parent_id'] = $data['parent_id'];
        $mainTable['position'] = $data['position'];
        $mainTable['level'] = (int) $data['level'];
        $mainTable['is_root'] = $data['is_root'] ? '1' : '0';
        $mainTable['menu_id'] = $data['menu'];
        $mainTable['type'] = $data['type'];
        $mainTable['identity'] = $data['identity'];
        $mainTable['hash'] = $data['hash'];
        $mainTable['target'] = $data['target'];

        if ($foreignLocale === false) {
            $mainTable['name'] = $data['name'];
            $mainTable['visibility'] = $data['visibility'] ? '1' : '0';
        }

        $this->connection->update('#__menu_item', $mainTable, ['id' => $data['id']]);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['menu_item_id'] = $data['id'];
        $langTable['locale'] = $data['locale'];
        $langTable['name'] = $data['name'];
        $langTable['visibility'] = $data['visibility'] ? '1' : '0';

        $this->connection->insert('#__menu_item_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['name'] = $data['name'];
        $langTable['visibility'] = $data['visibility'] ? '1' : '0';

        $this->connection->update('#__menu_item_lang', $langTable, [
            'menu_item_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT menu_item_id FROM #__menu_item_lang WHERE menu_item_id = :id AND locale = :locale LIMIT 1',
            ['id' => $data['id'], 'locale' => $data['locale']]
        );

        return isset($result[0]['menu_item_id']) && $result[0]['menu_item_id'] === $data['id'];
    }
}
