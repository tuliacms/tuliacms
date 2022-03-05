<?php

declare(strict_types = 1);

namespace Tulia\Cms\Widget\Domain\Catalog\Configuration;

use ArrayIterator;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayConfiguration implements ConfigurationInterface
{
    protected ?string $space;
    protected array $config;
    protected array $multilingualFields = [];

    public function __construct(?string $space = null, array $config = [])
    {
        $this->space  = $space;
        $this->config = $config;
    }

    public function multilingualFields(array $fields): void
    {
        $this->multilingualFields = $fields;
    }

    public function getMultilingualFields(): array
    {
        return $this->multilingualFields;
    }

    public function isMultilingual(string $name): bool
    {
        return \in_array($name, $this->multilingualFields, true);
    }

    public function allMultilingual(): array
    {
        $payload = [];

        foreach ($this->all() as $name => $val) {
            if ($this->isMultilingual($name)) {
                $payload[$name] = $val;
            }
        }

        return $payload;
    }

    public function allNotMultilingual(): array
    {
        $payload = [];

        foreach ($this->all() as $name => $val) {
            if ($this->isMultilingual($name) === false) {
                $payload[$name] = $val;
            }
        }

        return $payload;
    }

    public function getSpace(): ?string
    {
        return $this->space;
    }

    public function setSpace(?string $space): void
    {
        $this->space = $space;
    }

    public function get(string $name, $default = null)
    {
        return $this->config[$name] ?? $default;
    }

    public function set(string $name, $value): void
    {
        $this->config[$name] = $value;
    }

    public function has(string $name): bool
    {
        return isset($this->config[$name]);
    }

    public function all(): array
    {
        return $this->config;
    }

    public function defaults(array $defaults = []): void
    {
        $this->config = $defaults;
    }

    public function merge(array $import = []): void
    {
        $this->config = array_merge($this->config, $import);
    }

    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->config);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->config[$offset]);
    }

    /**
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset !== null) {
            $this->config[$offset] = $value;
        } else {
            $this->config[] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->config[$offset]);
    }
}
