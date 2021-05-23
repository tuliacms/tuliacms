<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface MetadataWriteStorageInterface
{
    public function find(string $type, array $ownerIdList, string $locale): array;

    public function persist(array $metadata, string $defaultLocale): void;

    public function insert(array $data, string $defaultLocale): void;

    public function update(array $data, string $defaultLocale): void;

    public function delete(string $type, string $ownerId): void;
}
