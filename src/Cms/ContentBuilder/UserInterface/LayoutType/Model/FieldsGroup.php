<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsGroup
{
    private string $name;
    private string $label;
    private bool $active;
    private string $interior;
    private array $fields;

    public function __construct(string $name, string $label, bool $active, string $interior, array $fields)
    {
        $this->name = $name;
        $this->label = $label;
        $this->active = $active;
        $this->interior = $interior;
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

    public function getInterior(): string
    {
        return $this->interior;
    }

    public function setInterior(string $interior): void
    {
        $this->interior = $interior;
    }
}
