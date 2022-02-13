<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\ReadModel;

use Tulia\Cms\Attributes\Domain\ReadModel\Model\AttributeValue;
use Tulia\Cms\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;

/**
 * @author Adam Banaszkiewicz
 */
class AttributesFinder
{
    private AttributeReadStorageInterface $finder;
    private UriToArrayTransformer $uriToArrayTransformer;

    public function __construct(
        AttributeReadStorageInterface $finder,
        UriToArrayTransformer $uriToArrayTransformer
    ) {
        $this->finder = $finder;
        $this->uriToArrayTransformer = $uriToArrayTransformer;
    }

    public function findAllAggregated(string $type, array $ownerIdList): array
    {
        $source = $this->finder->findAll($type, $ownerIdList);
        $result = [];

        foreach ($source as $ownerId => $attributes) {
            foreach ($attributes as $uri => $attribute) {
                if ($attribute['has_nonscalar_value']) {
                    try {
                        $value = (array) unserialize(
                            (string) $attribute['value'],
                            ['allowed_classes' => []]
                        );
                    } catch (\ErrorException $e) {
                        // If error, than empty or cannot be unserialized from singular value
                    }
                } else {
                    $value = $attribute['value'];
                }

                $attributes[$uri] = $value;
            }

            $result[$ownerId] = $this->uriToArrayTransformer->transform($attributes);

            foreach ($result[$ownerId] as $key => $value) {
                $result[$ownerId][$key] = new AttributeValue($value);
            }
        }

        return $result;
    }

    public function findAll(string $type, string $ownerId): array
    {
        return $this->findAllAggregated($type, [$ownerId])[$ownerId] ?? [];
    }
}
