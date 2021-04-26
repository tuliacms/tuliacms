<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Query\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Collection implements CollectionInterface
{
    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->replace($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->elements;
    }

    /**
     * {@inheritdoc}
     */
    public function append($element): void
    {
        if (! $element instanceof Website) {
            throw new \InvalidArgumentException(sprintf('Element must be instance of %s', Website::class));
        }

        $this->elements[] = $element;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(CollectionInterface $collection): void
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
    public function first(): ?Website
    {
        return $this->elements[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $array = [];

        /** @var Website $element */
        foreach ($this->elements as $element) {
            $array[] = $element->toArray();
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
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
    public function offsetSet($offset, $value)
    {
        if (! $value instanceof Website) {
            throw new \InvalidArgumentException(sprintf('Element must be instance of %s', Website::class));
        }

        if ($offset !== null) {
            $this->elements[$offset] = $value;
        } else {
            $this->elements[] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }
}
