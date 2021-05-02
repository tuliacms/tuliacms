<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Model;

use Tulia\Cms\Menu\Domain\WriteModel\Exception\ParentItemReccurencyException;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\ItemId;

/**
 * @author Adam Banaszkiewicz
 */
class Item
{
    protected ItemId $id;
    protected ?Menu $menu = null;
    protected int $position;
    protected ?Item $parent = null;
    protected int $level;
    protected ?string $type = null;
    protected ?string $identity = null;
    protected ?string $hash = null;
    protected ?string $target = null;
    protected string $locale;
    protected ?string $name = null;
    protected bool $visibility;
    protected array $metadata = [];

    public function __construct(ItemId $id, string $locale)
    {
        $this->id = $id;
        $this->locale = $locale;
    }

    public function getId(): ItemId
    {
        return $this->id;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): void
    {
        $this->menu = $menu;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getIdentity(): ?string
    {
        return $this->identity;
    }

    public function setIdentity(?string $identity): void
    {
        $this->identity = $identity;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getVisibility(): bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getParent(): ?Item
    {
        return $this->parent;
    }

    /**
     * @param null|Item $parent
     * @throws ParentItemReccurencyException
     */
    public function setParent(?Item $parent): void
    {
        if ($this->parent !== $parent) {
            if ($parent instanceof self) {
                $this->detectParentReccurency($parent);
                $this->level = $parent->level + 1;
            }

            $this->parent = $parent;
        }
    }

    /**
     * @param Item $item
     * @throws ParentItemReccurencyException
     */
    private function detectParentReccurency(Item $item): void
    {
        do {
            $parent = $item->getParent();

            if ($parent && ($parent->getId()->equals($this->getId()) || $parent->getId()->equals($item->getId()))) {
                throw new ParentItemReccurencyException();
            }
        } while ($item->getParent());
    }
}
