<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsGroup
{
    private string $code;
    private string $name;
    private bool $active;
    private string $interior;
    private array $fields;

    public function __construct(string $code, string $name, bool $active, string $interior, array $fields)
    {
        $this->code = $code;
        $this->name = $name;
        $this->active = $active;
        $this->interior = $interior;
        $this->fields = $fields;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
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
