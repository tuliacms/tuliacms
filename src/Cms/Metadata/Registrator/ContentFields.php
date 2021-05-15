<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Registrator;

/**
 * @author Adam Banaszkiewicz
 */
class ContentFields implements ContentFieldsInterface
{
    protected array $fields = [];

    public function add(array $field): ContentFieldsInterface
    {
        $field = array_merge([
            'name' => null,
            // Default value
            'default' => null,
            'multilingual' => true,
            // One of available: string, array, datetime
            'datatype' => 'string',
        ], $field);

        switch ($field['datatype']) {
            case 'array':
                $field['default'] = is_array($field['default']) ? $field['default'] : [];
        }

        $this->fields[$field['name']] = $field;

        return $this;
    }

    public function remove(string $field): ContentFieldsInterface
    {
        unset($this->fields[$field['name']]);

        return $this;
    }

    public function empty(): ContentFieldsInterface
    {
        $this->fields = [];

        return $this;
    }

    public function count(): int
    {
        return count($this->fields);
    }

    public function has($name): bool
    {
        return isset($this->fields[$name]);
    }

    public function get($name): array
    {
        return isset($this->fields[$name]) ? $this->fields[$name] : [];
    }

    public function all(): array
    {
        return $this->fields;
    }

    public function getNames(): array
    {
        return array_keys($this->fields);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }

    public function offsetExists($offset)
    {
        return isset($this->fields[$key]);
    }

    public function offsetGet($offset)
    {
        return $this->fields[$key];
    }

    public function offsetSet($offset, $value)
    {
        if (\is_array($value)) {
            throw new \InvalidArgumentException('Field must be array.');
        }

        $value['name'] = $offset;

        $this->add($value);
    }

    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }
}
