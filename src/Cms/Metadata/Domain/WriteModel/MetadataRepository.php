<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\WriteModel;

use Tulia\Cms\Metadata\Domain\Registry\DatatypeResolver;
use Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\WriteModel\MetadataStorageInterface;
use Tulia\Cms\Metadata\Registrator\RegistryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataRepository
{
    private MetadataStorageInterface $storage;
    private RegistryInterface $registry;
    private CurrentWebsiteInterface $currentWebsite;
    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        MetadataStorageInterface $storage,
        RegistryInterface $registry,
        CurrentWebsiteInterface $currentWebsite,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->storage = $storage;
        $this->registry = $registry;
        $this->currentWebsite = $currentWebsite;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function findAll(string $type, array $ownerIdList): array
    {
        $metadata = $this->storage->find($type, $ownerIdList, $this->currentWebsite->getLocale()->getCode());
        $fields = $this->registry->getContentFields($type);
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

    public function persist(string $type, string $ownerId, array $metadata): void
    {
        $datatypeResolver = new DatatypeResolver();
        $locale = $this->currentWebsite->getLocale()->getCode();
        $fields = $this->registry->getContentFields($type);

        foreach ($metadata as $name => $value) {
            foreach ($fields->all() as $field => $details) {
                if ($field === $name) {
                    $metadata[$name] = [
                        'id' => $this->uuidGenerator->generate(),
                        'value' => $datatypeResolver->reverseResolve(
                            $value,
                            $details['datatype'],
                            $field,
                            $ownerId
                        ),
                        'owner_id' => $ownerId,
                        'name' => $name,
                        'locale' => $locale,
                        'type' => $type,
                        'multilingual' => $details['multilingual'],
                    ];
                }
            }
        }

        $this->storage->persist(
            $metadata,
            $this->currentWebsite->getDefaultLocale()->getCode()
        );
    }
}
