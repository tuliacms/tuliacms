<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBlock\Domain\Renderer;

/**
 * @author Adam Banaszkiewicz
 */
class FieldValue implements \Stringable, \ArrayAccess
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function __toString(): string
    {
        return implode(', ', $this->values);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->values[$offset]);
    }
}
