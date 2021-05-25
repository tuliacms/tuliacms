<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Domain\Links;

/**
 * @author Adam Banaszkiewicz
 */
class Link
{
    private string $label;

    private string $href;

    private ?string $icon;

    private array $attributes = [];

    /**
     * @var Link[]
     */
    private array $children = [];

    public function __construct(string $label, string $href, ?string $icon = null)
    {
        $this->label = $label;
        $this->href = $href;
        $this->icon = $icon;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function setHref(string $href): void
    {
        $this->href = $href;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function addAttribute(string $name, string $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function removeAttribute(string $name): void
    {
        unset($this->attributes[$name]);
    }

    /**
     * @return Link[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param Link[] $children
     */
    public function setChildren(array $children): void
    {
        foreach ($children as $name => $child) {
            $this->addChild($name, $child);
        }
    }

    public function addChild(string $name, Link $link): void
    {
        $this->children[$name] = $link;
    }

    public function removeChild(string $name): void
    {
        unset($this->children[$name]);
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }
}
