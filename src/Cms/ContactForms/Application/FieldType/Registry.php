<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var TypeInterface[]
     */
    private array $types = [];
    private iterable $sourceTypes;

    public function __construct(iterable $sourceTypes)
    {
        $this->sourceTypes = $sourceTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $type): TypeInterface
    {
        $this->prepareTypes();

        return $this->types[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $type): bool
    {
        $this->prepareTypes();

        return isset($this->types[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function add(TypeInterface $type): void
    {
        $this->types[\get_class($type)] = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        $this->prepareTypes();

        return $this->types;
    }

    protected function prepareTypes(): void
    {
        if ($this->types !== []) {
            return;
        }

        foreach ($this->sourceTypes as $type) {
            $this->add($type);
        }
    }
}
