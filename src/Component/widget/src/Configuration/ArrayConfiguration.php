<?php

declare(strict_types = 1);

namespace Tulia\Component\Widget\Configuration;

use ArrayIterator;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayConfiguration implements ConfigurationInterface
{
    /**
     * @var null|string
     */
    protected $space = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $multilingualFields = [];

    /**
     * @param string|null $space
     * @param array $config
     */
    public function __construct(?string $space = null, array $config = [])
    {
        $this->space  = $space;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function multilingualFields(array $fields): void
    {
        $this->multilingualFields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultilingualFields(): array
    {
        return $this->multilingualFields;
    }

    /**
     * {@inheritdoc}
     */
    public function isMultilingual(string $name): bool
    {
        return \in_array($name, $this->multilingualFields, true);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getSpace(): ?string
    {
        return $this->space;
    }

    /**
     * {@inheritdoc}
     */
    public function setSpace(?string $space): void
    {
        $this->space = $space;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->config[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->config[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->config[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function defaults(array $defaults = []): void
    {
        $this->config = $defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $import = []): void
    {
        $this->config = array_merge($this->config, $import);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->config);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ($offset !== null) {
            $this->config[$offset] = $value;
        } else {
            $this->config[] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }
}
