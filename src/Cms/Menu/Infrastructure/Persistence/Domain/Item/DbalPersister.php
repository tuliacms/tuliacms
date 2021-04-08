<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Item;

use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizablePersister;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalPersister extends AbstractLocalizablePersister
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
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
            $mainTable['visibility'] = $data['visibility'];
        }

        $this->connection->update('#__menu_item', $mainTable, ['id' => $data['id']]);
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
        $mainTable['visibility'] = $data['visibility'];

        $this->connection->insert('#__menu_item', $mainTable);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['menu_item_id'] = $data['id'];
        $langTable['locale']       = $data['locale'];
        $langTable['name']         = $data['name'];
        $langTable['visibility']   = $data['visibility'];

        $this->connection->insert('#__menu_item_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['name']       = $data['name'];
        $langTable['visibility'] = $data['visibility'];

        $this->connection->update('#__menu_item_lang', $langTable, [
            'menu_item_id' => $data['id'],
            'locale'       => $data['locale'],
        ]);
    }

    protected function rootExists(string $id): bool
    {
        $result = $this->connection->fetchAllAssociative('SELECT id FROM #__menu_item WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }

    protected function langExists(string $id, string $locale): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT menu_item_id FROM #__menu_item_lang WHERE menu_item_id = :id AND locale = :locale LIMIT 1',
            ['id' => $id, 'locale' => $locale]
        );

        return isset($result[0]['menu_item_id']) && $result[0]['menu_item_id'] === $id;
    }

    public function delete(string $menuItemId): void
    {
        $this->connection->delete('#__menu_item', ['id' => $menuItemId]);
        $this->connection->delete('#__menu_item_lang', ['menu_item_id' => $menuItemId]);
    }
}
