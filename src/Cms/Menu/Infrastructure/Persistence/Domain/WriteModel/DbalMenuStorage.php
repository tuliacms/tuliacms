<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use PDO;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\MenuStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMenuStorage implements MenuStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $id): ?array
    {
        $menu = $this->connection->fetchAll(
            'SELECT * FROM #__menu AS tm WHERE tm.id = :id LIMIT 1',
            ['id' => $id]
        );

        if ($menu === []) {
            return null;
        }

        return reset($menu);
    }

    public function insert(array $menu): void
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
    }

    public function update(array $menu): void
    {
        $this->connection->update('#__menu', [
            'name' => $menu['name'],
        ], [
            'id' => $menu['id'],
        ], [
            'id' => PDO::PARAM_STR,
            'name' => PDO::PARAM_STR,
        ]);
    }

    public function delete(string $id): void
    {
        $this->connection->delete('#__menu', ['id' => $id], ['id' => PDO::PARAM_STR]);
    }
}
