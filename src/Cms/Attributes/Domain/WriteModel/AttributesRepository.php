<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\WriteModel;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Attributes\Domain\WriteModel\Service\AttributesWriteStorageInterface;
use Tulia\Cms\Shared\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class AttributesRepository
{
    private AttributesWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        AttributesWriteStorageInterface $storage,
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
                'is_multilingual' => $attribute->isMultilingual(),
                'is_renderable' => $attribute->is('renderable'),
                'has_nonscalar_value' => $attribute->hasNonscalarValue(),
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
