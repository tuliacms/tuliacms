<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\FieldTypeNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeMappingRegistry
{
    private ConstraintTypeMappingRegistry $constraintTypeMappingRegistry;
    private array $mapping = [];
    private bool $mappingResolved = false;

    public function __construct(ConstraintTypeMappingRegistry $constraintTypeMappingRegistry)
    {
        $this->constraintTypeMappingRegistry = $constraintTypeMappingRegistry;
    }

    public function addMapping(string $type, array $mapingInfo): void
    {
        $this->mapping[$type] = $mapingInfo;
    }

    public function all(): array
    {
        $this->resolveMapping();

        return $this->mapping;
    }

    public function allForContentType(string $contentTypeType): array
    {
        $this->resolveMapping();

        $result = [];

        foreach ($this->mapping as $type => $map) {
            if (in_array($contentTypeType, $map['exclude_for_types'])) {
                continue;
            }

            $result[$type] = $map;
        }

        return $result;
    }

    /**
     * @throws FieldTypeNotExistsException
     */
    public function getTypeClassname(string $type): string
    {
        $this->resolveMapping();

        if (isset($this->mapping[$type]['classname']) === false) {
            throw FieldTypeNotExistsException::fromName($type);
        }

        return $this->mapping[$type]['classname'];
    }

    public function getTypeBuilder(string $type): ?string
    {
        $this->resolveMapping();

        return $this->mapping[$type]['builder'] ?? null;
    }

    public function get(string $type): array
    {
        $this->resolveMapping();

        return $this->mapping[$type];
    }

    public function getTypeFlags(string $type): array
    {
        $this->resolveMapping();

        return isset($this->mapping[$type]) ? $this->mapping[$type]['flags'] : [];
    }

    public function hasType(string $type): bool
    {
        $this->resolveMapping();

        return isset($this->mapping[$type]);
    }

    private function resolveMapping(): void
    {
        if ($this->mappingResolved) {
            return;
        }

        foreach ($this->mapping as $typeKey => $val) {
            $constraints = [];

            foreach ($this->mapping[$typeKey]['constraints'] as $constraint) {
                $constraints[$constraint] = $this->constraintTypeMappingRegistry->get($constraint);
            }

            $this->mapping[$typeKey]['constraints'] = array_merge(
                $constraints,
                $this->mapping[$typeKey]['custom_constraints']
            );

            // Remove custom constraints as those were merged with named constraints
            unset($this->mapping[$typeKey]['custom_constraints']);
        }

        $this->mappingResolved = true;
    }
}
