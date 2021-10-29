<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

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

    public function getTypeClassname(string $type): string
    {
        return $this->mapping[$type]['classname'];
    }

    public function hasType(string $type): bool
    {
        return isset($this->mapping[$type]);
    }
}
