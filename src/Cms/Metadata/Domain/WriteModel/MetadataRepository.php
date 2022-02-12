<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\WriteModel;

use Tulia\Cms\Metadata\Domain\WriteModel\Model\Attribute;
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

    public function findAllAggregated(string $type, array $ownerIdList, array $info): array
    {
        $source = $this->storage->find($type, $ownerIdList, array_keys($info), $this->currentWebsite->getLocale()->getCode());
        $result = [];

        foreach ($source as $ownerId => $fields) {
            foreach ($fields as $key => $element) {
                $value = $element['value'];

                if ($info[$element['name']]['has_nonscalar_value']) {
                    try {
                        $value = (array) unserialize(
                            (string) $element['value'],
                            ['allowed_classes' => []]
                        );
                    } catch (\ErrorException $e) {
                        // If error, than empty or cannot be unserialized from singular value
                    }
                }

                $flags = [];

                if ($info[$element['name']]['is_compilable']) {
                    $flags[] = 'compilable';
                }

                $result[$ownerId][$key] = new Attribute(
                    $element['name'],
                    $value,
                    $element['uri'],
                    $flags,
                    $info[$element['name']]['is_multilingual'],
                    $info[$element['name']]['has_nonscalar_value'],
                );
            }
        }

        return $result;
    }

    public function findAll(string $type, string $ownerId, array $info): array
    {
        return $this->findAllAggregated($type, [$ownerId], $info)[$ownerId] ?? [];
    }

    /**
     * @param Attribute[] $metadata
     */
    public function persist(string $type, string $ownerId, array $metadata): void
    {
        $locale = $this->currentWebsite->getLocale()->getCode();
        $structure = [];

        foreach ($metadata as $uri => $attribute) {
            $structure[$uri] = [
                'id' => $this->uuidGenerator->generate(),
                'value' => $attribute->hasNonscalarValue() ? serialize($attribute->getValue()) : $attribute->getValue(),
                'owner_id' => $ownerId,
                'name' => $attribute->getCode(),
                'uri' => $uri,
                'locale' => $locale,
                'type' => $type,
                'multilingual' => $attribute->isMultilingual(),
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
