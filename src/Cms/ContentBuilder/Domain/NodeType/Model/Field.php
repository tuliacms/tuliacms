<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Field
{
    private string $name;
    private string $type;
    private string $label;
    private bool $isTitle;
    private bool $isSlug;
    private bool $multilingual;
    private bool $multiple;
    private array $constraints;
    private array $options;
    private array $flags;

    public function __construct(
        string $name,
        string $type,
        string $label,
        bool $isTitle,
        bool $isSlug,
        bool $multilingual,
        bool $multiple,
        array $constraints,
        array $options,
        array $flags = []
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->isTitle = $isTitle;
        $this->isSlug = $isSlug;
        $this->multilingual = $multilingual;
        $this->multiple = $multiple;
        $this->constraints = $constraints;
        $this->options = $options;
        $this->flags = $flags;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isMultilingual(): bool
    {
        return $this->multilingual;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isTitle(): bool
    {
        return $this->isTitle;
    }

    public function isSlug(): bool
    {
        return $this->isSlug;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function getOptions(array $mergeWithOptions = []): array
    {
        return $this->options;
    }

    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    public function hasFlag(string $flag): bool
    {
        return in_array($flag, $this->flags, true);
    }
}
