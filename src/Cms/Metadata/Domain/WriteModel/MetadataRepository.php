<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\WriteModel;

use Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\WriteModel\MetadataWriteStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataRepository
{
    private MetadataWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        MetadataWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function findAllAggregated(string $type, array $ownerIdList): array
    {
        return $this->storage->find($type, $ownerIdList, $this->currentWebsite->getLocale()->getCode());
    }

    public function findAll(string $type, string $ownerId): array
    {
        return $this->findAllAggregated($type, [$ownerId])[$ownerId] ?? [];
    }

    public function persist(string $type, string $ownerId, array $metadata): void
    {
        $locale = $this->currentWebsite->getLocale()->getCode();
        $structure = [];

        foreach ($metadata as $name => $info) {
            $structure[$name] = [
                'id' => $this->uuidGenerator->generate(),
                'value' => $info['is_multiple'] ? serialize($info['value']) : $info['value'],
                'owner_id' => $ownerId,
                'name' => $name,
                'locale' => $locale,
                'type' => $type,
                'multilingual' => $info['is_multilingual'],
                'multiple' => $info['is_multiple'],
            ];
        }

        $this->storage->persist(
            $structure,
            $this->currentWebsite->getDefaultLocale()->getCode()
        );
    }

    public function delete(string $type, string $ownerId): void
    {
        $this->storage->delete($type, $ownerId);
    }
}
