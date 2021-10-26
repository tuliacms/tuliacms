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
    private ?string $label = null;
    private bool $isTitle = false;
    private bool $isSlug = false;
    private array $constraints;
    private array $options;

    public function __construct(string $name, string $type, array $constraints, array $options)
    {
        $this->name = $name;
        $this->type = $type;
        $this->constraints = $constraints;
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): ?string
    {
        return $this->label ?? $this->name;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function isTitle(): bool
    {
        return $this->isTitle;
    }

    public function setIsTitle(bool $isTitle): void
    {
        $this->isTitle = $isTitle;
    }

    public function isSlug(): bool
    {
        return $this->isSlug;
    }

    public function setIsSlug(bool $isSlug): void
    {
        $this->isSlug = $isSlug;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function setConstraints(array $constraints): void
    {
        $this->constraints = $constraints;
    }

    public function getOptions(array $mergeWithOptions = []): array
    {
        return array_merge($mergeWithOptions, $this->options);
    }

    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }
}
