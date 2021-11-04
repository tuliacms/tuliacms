<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\ReadModel;

use Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\ReadModel\MetadataReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataFinder
{
    private MetadataReadStorageInterface $finder;

    public function __construct(MetadataReadStorageInterface $finder)
    {
        $this->finder = $finder;
    }

    public function findAllAggregated(string $type, array $ownerIdList): array
    {
        return $this->finder->findAll($type, $ownerIdList);
    }

    public function findAll(string $type, string $ownerId): array
    {
        return $this->findAllAggregated($type, [$ownerId])[$ownerId] ?? [];
    }
}
