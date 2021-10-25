<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsGroup
{
    private string $name;
    private array $fields;
    private string $label;
    private bool $active;

    public function __construct(string $name, string $label, bool $active, array $fields)
    {
        $this->name = $name;
        $this->fields = $fields;
        $this->label = $label;
        $this->active = $active;
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
