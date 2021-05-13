<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\ReadModel;

/**
 * @property array metadata
 * @author Adam Banaszkiewicz
 */
trait MagickMetadataTrait
{
    protected array $metadata = [];

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->{$name} ?? $this->metadata[$name] ?? null;
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
            $this->metadata[$name] = $value;
        }
    }

    public function __isset(string $name): bool
    {
        return method_exists($this, $name) || isset($this->metadata[$name]);
    }

    public function meta(string $name, $default = null)
    {
        return $this->metadata[$name] ?? $default;
    }

    public function getAllMetadata(): array
    {
        return $this->metadata;
    }

    public function replaceMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
