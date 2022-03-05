<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\ReadModel;

use Tulia\Cms\Attributes\Domain\ReadModel\Model\AttributeValue;

/**
 * @property array $attributes
 * @author Adam Banaszkiewicz
 */
trait MagickAttributesTrait
{
    protected array $attributes = [];

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->{"$name:compiled"} ?? $this->{$name} ?? $this->attributes[$name] ?? null;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        if (method_exists($this, $name)) {
            $this->{$name} = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }

    public function __call(string $name , array $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        return $this->attribute($name);
    }

    public function __isset(string $name): bool
    {
        return method_exists($this, $name) || isset($this->attributes[$name]) || isset($this->attributes["$name:compiled"]);
    }

    public function attribute(string $name, $default = null)
    {
        return $this->attributes["$name:compiled"] ?? $this->attributes[$name] ?? $default;
    }

    /**
     * @return AttributeValue[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param AttributeValue[] $attributes
     */
    public function replaceAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @param AttributeValue[] $attributes
     */
    public function mergeAttributes(array $attributes): void
    {
        $this->attributes += $attributes;
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->attributes) || array_key_exists("$offset:compiled", $this->attributes);
    }

    public function offsetGet($offset): ?AttributeValue
    {
        return $this->attributes["$offset:compiled"] ?? $this->attributes[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (isset($this->attributes["$offset:compiled"])) {
            $this->attributes["$offset:compiled"] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset], $this->attributes["$offset:compiled"]);
    }
}
