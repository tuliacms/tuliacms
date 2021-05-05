<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata;

/**
 * @author Adam Banaszkiewicz
 */
class Metadata implements MetadataInterface
{
    protected array $metadata = [];

    public function __construct(array $metadata = [])
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->metadata[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->metadata[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $metadata): void
    {
        foreach ($metadata as $key => $val) {
            $this->set($key, $val);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function keys(): array
    {
        return array_keys($this->metadata);
    }
}
