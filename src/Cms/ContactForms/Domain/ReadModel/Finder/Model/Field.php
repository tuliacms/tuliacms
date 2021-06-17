<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\ReadModel\Finder\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Field
{
    private string $name;

    private string $type;

    private string $typeAlias;

    private array $options = [];

    public static function buildFromArray(array $data): self
    {
        $self = new self();
        $self->name = $data['name'] ?? '';
        $self->type = $data['type'] ?? '';
        $self->typeAlias = $data['type_alias'] ?? '';
        $self->options = $data['options'] ?? [];

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getTypeAlias(): string
    {
        return $this->typeAlias;
    }

    public function setTypeAlias(string $typeAlias): void
    {
        $this->typeAlias = $typeAlias;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }
}
