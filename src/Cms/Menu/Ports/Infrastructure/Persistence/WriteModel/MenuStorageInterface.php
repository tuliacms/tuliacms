<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuStorageInterface
{
    public function commit();

    public function beginTransaction(): void;

    public function rollBack(): void;

    public function find(string $id, string $defaultLocale, string $locale): ?array;

    public function insert(array $menu, string $defaultLocale): void;

    public function update(array $menu, string $defaultLocale): void;

    public function delete(string $id): void;
}
