<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\FieldTypeNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeMappingRegistry
{
    private array $mapping = [];

    public function addMapping(string $type, array $mapingInfo): void
    {
        $this->mapping[$type] = $mapingInfo;
    }

    /**
     * @throws FieldTypeNotExistsException
     */
    public function getTypeClassname(string $type): string
    {
        if (isset($this->mapping[$type]['classname']) === false) {
            throw FieldTypeNotExistsException::fromName($type);
        }

        return $this->mapping[$type]['classname'];
    }

    public function getTypeFlags(string $type): array
    {
        return isset($this->mapping[$type]) ? $this->mapping[$type]['flags'] : [];
    }

    public function hasType(string $type): bool
    {
        return isset($this->mapping[$type]);
    }
}
