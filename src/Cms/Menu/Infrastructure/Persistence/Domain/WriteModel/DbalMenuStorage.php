<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

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
        // TODO: Implement find() method.
    }

    public function insert(array $menu): void
    {
        dump($menu);exit;
    }

    public function update(array $menu): void
    {
        // TODO: Implement update() method.
    }

    public function delete(string $id): void
    {
        // TODO: Implement delete() method.
    }
}
