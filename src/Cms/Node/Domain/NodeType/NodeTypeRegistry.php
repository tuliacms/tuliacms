<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeRegistry implements NodeTypeRegistryInterface
{
    protected array $types = [];

    protected iterable $registrators = [];

    protected iterable $storages = [];

    public function __construct(iterable $registrators = [], iterable $storages = [])
    {
        $this->registrators = $registrators;
        $this->storages = $storages;
    }

    public function all(): iterable
    {
        $this->callRegistrators();

        return $this->types;
    }

    public function registerType(string $type): NodeTypeInterface
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        return $this->types[$type] = new NodeType($type);
    }

    public function getType(string $type): NodeTypeInterface
    {
        $this->callRegistrators();

        if (!isset($this->types[$type])) {
            throw new \Exception(sprintf('Node type "%s" not exists.', $type));
        }

        return $this->types[$type];
    }

    public function isTypeRegistered(string $type): bool
    {
        $this->callRegistrators();

        return isset($this->types[$type]);
    }

    public function getRegisteredTypesNames(): iterable
    {
        $this->callRegistrators();

        return array_keys($this->types);
    }

    private function callRegistrators(): void
    {
        foreach ($this->registrators as $registrator) {
            $registrator->register($this);
        }

        foreach ($this->storages as $storage) {
            foreach ($storage->all() as $name => $type) {
                $this->types[$name] = $type;
            }
        }

        $this->registrators = [];
        $this->storages = [];
    }
}
