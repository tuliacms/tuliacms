<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Item
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var int
     */
    protected $level = 1;

    /*
     * @var string
     */
    protected $label;

    /*
     * @var string
     */
    protected $target;

    /*
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var IdentityInterface
     */
    protected $identity;

    /*
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $children = [];

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children): void
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children !== [];
    }

    /**
     * @param Item $child
     */
    public function addChild(Item $child): void
    {
        $this->children[] = $child;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->level === 1;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        if ($this->link) {
            return $this->link;
        }

        $link = $this->identity->getLink();

        if ($this->hash && strpos($link, '#') === false) {
            $link .= '#' . $this->hash;
        }

        return $link;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target): void
    {
        $this->target = $target;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return IdentityInterface
     */
    public function getIdentity(): IdentityInterface
    {
        return $this->identity;
    }

    /**
     * @param IdentityInterface $identity
     */
    public function setIdentity(IdentityInterface $identity): void
    {
        $this->identity = $identity;
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
     * @param mixed $value
     */
    public function setAttribute(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }
}
