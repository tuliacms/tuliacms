<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface AttributesWriteStorageInterface
{
    public function find(string $type, array $ownerIdList, array $attributes, string $locale): array;

    public function persist(array $attributes, string $defaultLocale): void;

    public function insert(array $data, string $defaultLocale): void;

    public function update(array $data, string $defaultLocale): void;

    public function delete(string $type, string $ownerId): void;
}
