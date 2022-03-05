<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy implements HierarchyInterface
{
    protected string $id;
    protected array $elements = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function append(Item $item): void
    {
        $this->elements[] = $item;
    }

    public function flatten(): HierarchyInterface
    {
        $elements = [];


    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    /**
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset !== null) {
            $this->elements[$offset] = $value;
        } else {
            $this->elements[] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }
}
