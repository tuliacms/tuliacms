<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\Links;

/**
 * @author Adam Banaszkiewicz
 */
class Link
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $href;

    /**
     * @var string|null
     */
    private $icon;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var Link[]
     */
    private $children = [];

    public function __construct(string $label, string $href, ?string $icon = null)
    {
        $this->label = $label;
        $this->href = $href;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @param string $href
     */
    public function setHref(string $href): void
    {
        $this->href = $href;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     */
    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addAttribute(string $name, string $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param string $name
     */
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

    /**
     * @param string $name
     * @param Link $link
     */
    public function addChild(string $name, Link $link): void
    {
        $this->children[$name] = $link;
    }

    /**
     * @param string $name
     */
    public function removeChild(string $name): void
    {
        unset($this->children[$name]);
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }
}
