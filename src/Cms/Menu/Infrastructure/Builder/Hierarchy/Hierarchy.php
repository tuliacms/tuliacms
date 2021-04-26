<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy implements HierarchyInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function append(Item $item): void
    {
        $this->elements[] = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function flatten(): HierarchyInterface
    {
        $elements = [];


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
