<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface ItemStorageInterface
{
    public function findAll(string $menuId, string $defaultLocale, string $locale): array;
    public function insert(array $item, string $defaultLocale): void;
    public function update(array $item, string $defaultLocale): void;
    public function delete(string $id): void;
}
