<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\WriteModel;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;

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
        return $this->{$name} ?? $this->attributes[$name] ?? null;
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

    public function __isset(string $name): bool
    {
        return method_exists($this, $name) || isset($this->attributes[$name]);
    }

    public function attribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function setAttribute(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
