<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
final class Field
{
    private string $name;

    private string $type;

    private array $options;

    private function __construct(string $name, string $type, array $options = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    public static function buildFromArray(array $data): self
    {
        return new self($data['name'], $data['type'], $data['options']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
