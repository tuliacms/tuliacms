<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
interface MetadataReadStorageInterface
{
    public function findAll(string $type, array $ownerId): array;
}
