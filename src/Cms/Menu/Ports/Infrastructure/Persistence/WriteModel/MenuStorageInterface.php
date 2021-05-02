<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuStorageInterface
{
    public function find(string $id): ?array;
    public function insert(array $menu): void;
    public function update(array $menu): void;
    public function delete(string $id): void;
}
