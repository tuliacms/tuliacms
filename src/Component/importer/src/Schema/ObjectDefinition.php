<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Schema;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectDefinition
{
    private string $name;
    /** @var Field[] */
    private array $fields;
    private ?string $importer;

    /**
     * @param Field[] $fields
     */
    public function __construct(
        string $name,
        array $fields,
        ?string $importer = null
    ) {
        $this->name = $name;
        $this->fields = $fields;
        $this->importer = $importer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    public function getField(string $name): Field
    {
        return $this->fields[$name];
    }

    public function getImporter(): ?string
    {
        return $this->importer;
    }
}
