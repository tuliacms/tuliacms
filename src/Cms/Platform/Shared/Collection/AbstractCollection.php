<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Collection;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractCollection implements \ArrayAccess, \IteratorAggregate
{
    protected array $elements = [];

    /**
     * @param mixed $element
     *
     * @throws \InvalidArgumentException
     */
    abstract protected function validateType($element): void;

    /**
     * @return mixed
     */
    abstract public function first();

    public function __construct(array $elements = [])
    {
        $this->replace($elements);
    }

    public function all(): array
    {
        return $this->elements;
    }

    /**
     * {@inheritdoc}
     */
    public function append($element): void
    {
        $this->validateType($element);

        $this->elements[] = $element;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($element): void
    {
        $this->validateType($element);

        if (($key = array_search($element, $this->elements, true)) !== false) {
            unset($this->elements[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function merge(self $collection): void
    {
        foreach ($collection as $element) {
            $this->append($element);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $elements): void
    {
        $this->elements = [];

        foreach ($elements as $element) {
            $this->append($element);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($element): bool
    {
        $this->validateType($element);

        return \in_array($element, $this->elements, true);
    }

    public function keys(): array
    {
        return array_keys($this->elements);
    }

    public function empty(): void
    {
        $this->elements = [];
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->validateType($value);

        if ($offset !== null) {
            $this->elements[$offset] = $value;
        } else {
            $this->elements[] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }
}
