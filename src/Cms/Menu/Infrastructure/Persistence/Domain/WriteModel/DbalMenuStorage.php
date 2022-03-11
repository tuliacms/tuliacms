<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use PDO;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMenuStorage
{
    private ConnectionInterface $connection;
    private DbalItemStorage $itemStorage;

    public function __construct(
        ConnectionInterface $connection,
        DbalItemStorage $itemStorage
    ) {
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
        $menu = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__menu AS tm WHERE tm.id = :id LIMIT 1',
            ['id' => $id]
        );

        if ($menu === []) {
            return null;
        }

        $menu = reset($menu);
        $menu['items'] = $this->itemStorage->findAll($id, $defaultLocale, $locale);
        $menu['locale'] = $locale;

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

        $this->storeItems($menu['id'], $menu['items'], $defaultLocale, $menu['locale']);
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

        $this->storeItems($menu['id'], $menu['items'], $defaultLocale, $menu['locale']);
    }

    public function delete(string $id): void
    {
        $this->connection->delete('#__menu', ['id' => $id], ['id' => PDO::PARAM_STR]);
    }

    public function exists(string $menuId): bool
    {
        return (bool) $this->connection->fetchFirstColumn('SELECT id FROM #__menu WHERE id = :id', ['id' => $menuId]);
    }

    private function storeItems(string $menuId, array $items, string $defaultLocale, string $locale): void
    {
        $currentItemsSource = $this->itemStorage->findAll($menuId, $defaultLocale, $locale);
        $currentItems = [];
        $newItems = [];

        foreach ($currentItemsSource as $item) {
            $currentItems[$item['id']] = $item;
        }
        foreach ($items as $item) {
            $newItems[$item['id']] = $item;
        }

        $toAdd = array_diff_key($newItems, $currentItems);
        $toDelete = array_diff_key($currentItems, $newItems);
        $toUpdate = array_diff_key($newItems, $toAdd + $toDelete);

        foreach ($toAdd as $item) {
            $item['menu_id'] = $menuId;
            $this->itemStorage->insert($item, $defaultLocale);
        }
        foreach ($toUpdate as $item) {
            $item['menu_id'] = $menuId;
            $this->itemStorage->update($item, $defaultLocale);
        }
        foreach ($toDelete as $item) {
            $this->itemStorage->delete($item['id']);
        }
    }
}
