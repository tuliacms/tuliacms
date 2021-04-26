<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

/**
 * @author Adam Banaszkiewicz
 */
class LazyServiceIterator implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $names = [];

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $objects = [];

    /**
     * @var bool
     */
    protected $fetched = false;

    /**
     * @param ContainerInterface $container
     * @param array $names
     * @param array $parameters
     */
    public function __construct(ContainerInterface $container, array $names = [], array $parameters = [])
    {
        $this->container  = $container;
        $this->names      = $names;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $this->fetch();

        return new \ArrayIterator($this->objects);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(string $name): array
    {
        return $this->parameters[$name] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $this->fetch();

        return isset($this->objects[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $this->fetch();

        return $this->objects[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->fetch();

        if ($offset !== null) {
            $this->objects[$offset] = $value;
        } else {
            $this->objects[] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->fetch();

        unset($this->objects[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $this->fetch();

        return count($this->objects);
    }

    private function fetch(): void
    {
        if ($this->fetched) {
            return;
        }

        foreach ($this->names as $name) {
            $this->objects[$name] = $this->container->get($name);
        }

        $this->fetched = true;
    }
}
