<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\AbstractModel;

/**
 * @author Adam Banaszkiewicz
 */
class AbstractFieldsGroup
{
    protected string $code;
    protected string $name;
    protected array $fields;

    public function __construct(string $code, string $name, array $fields)
    {
        $this->code = $code;
        $this->name = $name;
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
}