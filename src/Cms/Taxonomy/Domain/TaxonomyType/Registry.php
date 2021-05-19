<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\Domain\TaxonomyType;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var array
     */
    protected $types = [];

    /**
     * @var iterable
     */
    protected $registrators = [];

    /**
     * @var iterable
     */
    protected $storages = [];

    /**
     * @param iterable $registrators
     * @param iterable $storages
     */
    public function __construct(iterable $registrators = [], iterable $storages = [])
    {
        $this->registrators = $registrators;
        $this->storages     = $storages;
    }

    /**
     * {@inheritdoc}
     */
    public function addRegistrator(RegistratorInterface $registrator): void
    {
        $this->registrators[] = $registrator;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): iterable
    {
        $this->callRegistrators();

        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function registerType(string $type): TaxonomyTypeInterface
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        return $this->types[$type] = new TaxonomyType($type);
    }

    /**
     * {@inheritdoc}
     */
    public function getType(string $type): TaxonomyTypeInterface
    {
        $this->callRegistrators();

        if (!isset($this->types[$type])) {
            throw new \Exception(sprintf('Taxonomy type "%s" not exists.', $type));
        }

        return $this->types[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeRegistered(string $type): bool
    {
        $this->callRegistrators();

        return isset($this->types[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisteredTypesNames(): iterable
    {
        $this->callRegistrators();

        return array_keys($this->types);
    }

    private function callRegistrators(): void
    {
        foreach ($this->registrators as $key => $registrator) {
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
