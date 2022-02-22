<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderRegistry;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\FieldTypeNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
// @todo Move Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry to Domain layer
class FieldTypeMappingRegistry
{
    private ConstraintTypeMappingRegistry $constraintTypeMappingRegistry;
    private array $mapping = [];
    private bool $mappingResolved = false;
    private FieldTypeBuilderRegistry $builderRegistry;
    private FieldTypeHandlerRegistry $handlerRegistry;

    public function __construct(
        ConstraintTypeMappingRegistry $constraintTypeMappingRegistry,
        FieldTypeBuilderRegistry $builderRegistry,
        FieldTypeHandlerRegistry $handlerRegistry
    ) {
        $this->constraintTypeMappingRegistry = $constraintTypeMappingRegistry;
        $this->builderRegistry = $builderRegistry;
        $this->handlerRegistry = $handlerRegistry;
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

    public function allForContentType(string $contentTypeCode): array
    {
        $this->resolveMapping();

        $result = [];

        foreach ($this->mapping as $type => $map) {
            if (\in_array($contentTypeCode, $map['exclude_for_types'], true)) {
                continue;
            }

            if (
                $map['only_for_types'] !== []
                && \in_array($contentTypeCode, $map['only_for_types'], true) === false
            ) {
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

    public function getTypeHandler(string $type): ?FieldTypeHandlerInterface
    {
        $this->resolveMapping();

        if (isset($this->mapping[$type]['handler']) === false) {
            return null;
        }

        return $this->handlerRegistry->has($this->mapping[$type]['handler'])
            ? $this->handlerRegistry->get($this->mapping[$type]['handler'])
            : null;
    }

    public function getTypeBuilder(string $type): ?FieldTypeBuilderInterface
    {
        $this->resolveMapping();

        if (isset($this->mapping[$type]['builder']) === false) {
            return null;
        }

        return $this->builderRegistry->has($this->mapping[$type]['builder'])
            ? $this->builderRegistry->get($this->mapping[$type]['builder'])
            : null;
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
