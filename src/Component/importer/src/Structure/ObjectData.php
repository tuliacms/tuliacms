<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Structure;

use Tulia\Component\Importer\Schema\ObjectDefinition;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectData implements \ArrayAccess
{
    private array $objectData;
    private ObjectDefinition $definition;

    public function __construct(array $objectData, ObjectDefinition $definition)
    {
        $this->objectData = $objectData;
        $this->definition = $definition;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->objectData as $field => $value) {
            if (is_array($value) && $this->definition->getField($field)->isCollection()) {
                foreach ($value as $k => $v) {
                    $value[$k] = $v->toArray();
                }
            }

            $result[$field] = $value;
        }

        return $result;
    }

    public function getDefinition(): ObjectDefinition
    {
        return $this->definition;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->objectData[$offset]);
    }

    /**
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->objectData[$offset] ?? $this->definition->getField($offset)->getDefaultValue();
    }

    public function offsetSet($offset, $value): void
    {
        $this->objectData[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->objectData[$offset]);
    }
}
