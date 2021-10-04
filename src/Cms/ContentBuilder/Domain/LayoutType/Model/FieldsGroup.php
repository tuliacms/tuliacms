<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\LayoutType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsGroup
{
    private string $name;
    private array $fields;

    public function __construct(string $name, string $label, array $fields)
    {
        $this->name = $name;
        $this->fields = $fields;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}
