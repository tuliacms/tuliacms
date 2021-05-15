<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\ReadModel;

use Tulia\Cms\Metadata\Domain\Registry\DatatypeResolver;
use Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\ReadModel\MetadataReadStorageInterface;
use Tulia\Cms\Metadata\Domain\Registry\ContentFieldsRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataFinder
{
    private ContentFieldsRegistryInterface $registry;

    private MetadataReadStorageInterface $finder;

    public function __construct(ContentFieldsRegistryInterface $registry, MetadataReadStorageInterface $finder)
    {
        $this->registry = $registry;
        $this->finder = $finder;
    }

    public function findAllAggregated(string $type, array $ownerIdList): array
    {
        $fields = $this->registry->getContentFields($type);
        $metadata = $this->finder->findAll($type, $ownerIdList);
        $result = [];

        $datatypeResolver = new DatatypeResolver();

        foreach ($ownerIdList as $ownerId) {
            foreach ($fields->all() as $field => $details) {
                if (isset($metadata[$ownerId][$field])) {
                    $result[$ownerId][$field] = $datatypeResolver->resolve(
                        $metadata[$ownerId][$field],
                        $details['datatype'],
                        $field,
                        $ownerId
                    );
                } else {
                    $result[$ownerId][$field] = $details['default'];
                }
            }
        }

        return $result;
    }

    public function findAll(string $type, string $ownerId): array
    {
        return $this->findAllAggregated($type, [$ownerId])[$ownerId] ?? [];
    }
}
