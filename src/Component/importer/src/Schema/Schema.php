<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Schema;

/**
 * @author Adam Banaszkiewicz
 */
class Schema
{
    /** @var ObjectDefinition[] */
    private array $objects;

    public function addObject(ObjectDefinition $object): void
    {
        $this->objects[$object->getName()] = $object;
    }

    public function has(string $name): bool
    {
        return isset($this->objects[$name]);
    }

    public function get(string $name): ObjectDefinition
    {
        return $this->objects[$name];
    }
}
