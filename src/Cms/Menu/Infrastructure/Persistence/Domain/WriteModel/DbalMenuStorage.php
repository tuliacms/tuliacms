<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use PDO;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\ItemStorageInterface;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\MenuStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMenuStorage implements MenuStorageInterface
{
    private ConnectionInterface $connection;
    private ItemStorageInterface $itemStorage;

    public function __construct(ConnectionInterface $connection, ItemStorageInterface $itemStorage)
    {
        $this->connection = $connection;
        $this->itemStorage = $itemStorage;
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function rollBack(): void
    {
        $this->connection->rollBack();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function find(string $id, string $defaultLocale, string $locale): ?array
    {
        $menu = $this->connection->fetchAll(
            'SELECT * FROM #__menu AS tm WHERE tm.id = :id LIMIT 1',
            ['id' => $id]
        );

        if ($menu === []) {
            return null;
        }

        $menu = reset($menu);
        $menu['items'] = $this->itemStorage->findAll($id, $defaultLocale, $locale);

        return $menu;
    }

    public function insert(array $menu, string $defaultLocale): void
    {
        $this->connection->insert('#__menu', [
            'id' => $menu['id'],
            'website_id' => $menu['website_id'],
            'name' => $menu['name'],
        ], [
            'id' => PDO::PARAM_STR,
            'website_id' => PDO::PARAM_STR,
            'name' => PDO::PARAM_STR,
        ]);

        $this->storeItems($menu['items'], $defaultLocale);
    }

    public function update(array $menu, string $defaultLocale): void
    {
        $this->connection->update('#__menu', [
            'name' => $menu['name'],
        ], [
            'id' => $menu['id'],
        ], [
            'id' => PDO::PARAM_STR,
            'name' => PDO::PARAM_STR,
        ]);

        $this->storeItems($menu['items'], $defaultLocale);
    }

    public function delete(string $id): void
    {
        $this->connection->delete('#__menu', ['id' => $id], ['id' => PDO::PARAM_STR]);
    }

    private function storeItems(array $items, string $defaultLocale): void
    {
        foreach ($items as $item) {
            if ($item['_change_type'] === 'add') {
                $this->itemStorage->insert($item, $defaultLocale);
            } elseif ($item['_change_type'] === 'update') {
                $this->itemStorage->update($item, $defaultLocale);
            } elseif ($item['_change_type'] === 'delete') {
                $this->itemStorage->delete($item['id']);
            }
        }
    }
}
