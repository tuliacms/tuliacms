<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item\Enum\MetadataEnum;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\ItemStorageInterface;
use Tulia\Cms\Metadata\Metadata;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalItemStorage extends AbstractLocalizableStorage implements ItemStorageInterface
{
    private ConnectionInterface $connection;
    private SyncerInterface $metadata;

    public function __construct(ConnectionInterface $connection, SyncerInterface $metadata)
    {
        $this->connection = $connection;
        $this->metadata = $metadata;
    }

    public function findAll(string $menuId, string $defaultLocale, string $locale): array
    {
        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.name), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        $items = $this->connection->fetchAll("
            SELECT
                tm.*,
                COALESCE(tl.locale, :defaultLocale) AS locale,
                COALESCE(tl.name, tm.name) AS name,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                {$translationColumn}
            FROM #__menu_item AS tm
            LEFT JOIN #__menu_item_lang AS tl
                ON tm.id = tl.menu_item_id AND tl.locale = :locale
            WHERE tm.menu_id = :menu_id
            ORDER BY tm.position ASC, tm.level ASC", [
            'menu_id' => $menuId,
            'locale'  => $locale,
            'defaultLocale' => $defaultLocale,
        ]);

        foreach ($items as $key => $item) {
            $items[$key]['metadata'] = $this->metadata->all(MetadataEnum::MENUITEM_GROUP, $item['id']);
        }

        return $items;
    }

    public function delete(string $id): void
    {
        $this->connection->delete('#__menu_item', ['id' => $id]);
        $this->connection->delete('#__menu_item_lang', ['id' => $id]);

        $this->metadata->delete(MetadataEnum::MENUITEM_GROUP, $id);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['id']         = $data['id'];
        $mainTable['parent_id']  = $data['parent'];
        $mainTable['level']      = (int) $data['level'];
        $mainTable['menu_id']    = $data['menu'];
        $mainTable['position']   = $data['position'];
        $mainTable['type']       = $data['type'];
        $mainTable['identity']   = $data['identity'];
        $mainTable['hash']       = $data['hash'];
        $mainTable['target']     = $data['target'];

        if ($foreignLocale === false) {
            $mainTable['name']       = $data['name'];
            $mainTable['visibility'] = $data['visibility'] ? '1' : '0';
        }

        $this->connection->update('#__menu_item', $mainTable, ['id' => $data['id']]);

        $this->metadata->push(new Metadata($data['metadata']), MetadataEnum::MENUITEM_GROUP, $data['id']);
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id']         = $data['id'];
        $mainTable['parent_id']  = $data['parent'];
        $mainTable['level']      = (int) $data['level'];
        $mainTable['menu_id']    = $data['menu'];
        $mainTable['position']   = $data['position'];
        $mainTable['type']       = $data['type'];
        $mainTable['identity']   = $data['identity'];
        $mainTable['hash']       = $data['hash'];
        $mainTable['target']     = $data['target'];
        $mainTable['name']       = $data['name'];
        $mainTable['visibility'] = $data['visibility'] ? '1' : '0';

        $this->connection->insert('#__menu_item', $mainTable);

        $this->metadata->push(new Metadata($data['metadata']), MetadataEnum::MENUITEM_GROUP, $data['id']);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['menu_item_id'] = $data['id'];
        $langTable['locale']       = $data['locale'];
        $langTable['name']         = $data['name'];
        $langTable['visibility']   = $data['visibility'] ? '1' : '0';

        $this->connection->insert('#__menu_item_lang', $langTable);

        $this->metadata->push(new Metadata($data['metadata']), MetadataEnum::MENUITEM_GROUP, $data['id']);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['name']       = $data['name'];
        $langTable['visibility'] = $data['visibility'] ? '1' : '0';

        $this->connection->update('#__menu_item_lang', $langTable, [
            'menu_item_id' => $data['id'],
            'locale'       => $data['locale'],
        ]);

        $this->metadata->push(new Metadata($data['metadata']), MetadataEnum::MENUITEM_GROUP, $data['id']);
    }

    protected function langExists(string $id, string $locale): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT menu_item_id FROM #__menu_item_lang WHERE menu_item_id = :id AND locale = :locale LIMIT 1',
            ['id' => $id, 'locale' => $locale]
        );

        return isset($result[0]['menu_item_id']) && $result[0]['menu_item_id'] === $id;
    }
}