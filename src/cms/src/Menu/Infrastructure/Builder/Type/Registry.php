<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Type;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var TypeInterface[]
     */
    protected $types = [];

    /**
     * @var RegistratorInterface[]
     */
    protected $registrators = [];

    /**
     * @param iterable $registrators
     */
    public function __construct(iterable $registrators = [])
    {
        $this->registrators = $registrators;
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
    public function all(): array
    {
        $this->callRegistrators();

        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function registerType(string $type): TypeInterface
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        return $this->types[$type] = new Type($type);
    }

    private function callRegistrators(): void
    {
        if ($this->registrators === []) {
            return;
        }

        foreach ($this->registrators as $key => $registrator) {
            $registrator->register($this);
        }

        $this->registrators = [];
    }
}
