<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\ReadModel;

use Tulia\Cms\Metadata\Ports\Infrastructure\Persistence\ReadModel\MetadataFinderInterface;
use Tulia\Cms\Metadata\Registrator\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataFinder
{
    private RegistryInterface $registry;
    private MetadataFinderInterface $finder;

    public function __construct(RegistryInterface $registry, MetadataFinderInterface $finder)
    {
        $this->registry = $registry;
        $this->finder = $finder;
    }

    public function findAll(string $type, array $ownerIdList): array
    {
        $fields = $this->registry->getContentFields($type);
        $metadata = $this->finder->findAll($type, $ownerIdList);
        $result = [];

        foreach ($ownerIdList as $ownerId) {
            foreach ($fields->all() as $field => $details) {
                if (isset($metadata[$ownerId][$field])) {
                    $value = $this->filterMetadataValueType(
                        $metadata[$ownerId][$field],
                        $details['datatype'],
                        $field,
                        $ownerId
                    );

                    $result[$ownerId][$field] = $value;
                } else {
                    $result[$ownerId][$field] = $details['default'];
                }
            }
        }

        return $result;
    }

    private function filterMetadataValueType($value, string $expectedType, string $name, string $ownerId)
    {
        if ($expectedType === 'string') {
            return (string) $value;
        } elseif ($expectedType === 'integer') {
            return (int) $value;
        } elseif ($expectedType === 'float') {
            return (float) $value;
        } elseif ($expectedType === 'array') {
            return \is_array($value) ? $value : json_decode($value, true);
        }

        throw new \InvalidArgumentException(sprintf('Value of %s ownered by %s has nod recognized datatype.', $name, $ownerId));
    }
}
