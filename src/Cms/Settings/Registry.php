<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var iterable
     */
    protected $factories = [];

    /**
     * @var iterable|array|GroupInterface[]
     */
    protected $groups = [];

    /**
     * @param iterable $factories
     * @param iterable $groups
     */
    public function __construct(iterable $factories = [], iterable $groups = [])
    {
        $this->factories = $factories;
        $this->groups    = $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function addGroupFactory(GroupFactoryInterface $factory): void
    {
        $this->factories[] = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): iterable
    {
        $this->callFactories();

        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup(string $id): bool
    {
        $this->callFactories();

        return isset($this->groups[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup(string $id): GroupInterface
    {
        $this->callFactories();

        return $this->groups[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups(): iterable
    {
        $this->callFactories();

        return $this->groups;
    }

    private function callFactories(): void
    {
        if ($this->factories === []) {
            return;
        }

        $groups = [];

        foreach ($this->groups as $group) {
            $groups[$group->getId()] = $group;
        }

        foreach ($this->factories as $key => $factory) {
            foreach ($factory->doFactory() as $group) {
                $groups[$group->getId()] = $group;
            }
        }

        $this->groups    = $groups;
        $this->factories = [];
    }
}
