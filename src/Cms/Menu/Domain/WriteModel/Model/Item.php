<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Model;

use Tulia\Cms\Menu\Domain\WriteModel\Exception\ParentItemReccurencyException;

/**
 * @author Adam Banaszkiewicz
 */
class Item
{
    protected string $id;
    protected ?Menu $menu = null;
    protected ?string $parentId = null;
    protected int $position;
    protected int $level;
    protected ?string $type = null;
    protected ?string $identity = null;
    protected ?string $hash = null;
    protected ?string $target = null;
    protected string $locale;
    protected bool $translated = false;
    protected ?string $name = null;
    protected bool $visibility;
    protected array $metadata = [];

    public function __construct(string $id, string $locale)
    {
        $this->id = $id;
        $this->locale = $locale;
    }

    public static function buildFromArray(array $data): self
    {
        $item = new self($data['id'], $data['locale']);
        $item->name = $data['name'] ?? null;
        $item->parentId = $data['parent_id'] ?? null;
        $item->position = (int) ($data['position'] ?? 0);
        $item->level = (int) ($data['level'] ?? 0);
        $item->type = $data['type'] ?? null;
        $item->identity = $data['identity'] ?? null;
        $item->hash = $data['hash'] ?? null;
        $item->target = $data['target'] ?? null;
        $item->locale = $data['locale'];
        $item->translated = (bool) ($data['translated'] ?? false);
        $item->name = $data['name'] ?? null;
        $item->visibility = (bool) ($data['visibility'] ?? 1);

        return $item;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->recordItemChanged();
        $this->parentId = $parentId;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->recordItemChanged();
        $this->position = $position;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->recordItemChanged();
        $this->level = $level;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->recordItemChanged();
        $this->type = $type;
    }

    public function getIdentity(): ?string
    {
        return $this->identity;
    }

    public function setIdentity(?string $identity): void
    {
        $this->recordItemChanged();
        $this->identity = $identity;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): void
    {
        $this->recordItemChanged();
        $this->hash = $hash;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): void
    {
        $this->recordItemChanged();
        $this->target = $target;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->recordItemChanged();
        $this->locale = $locale;
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    public function setTranslated(bool $translated): void
    {
        $this->translated = $translated;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->recordItemChanged();
        $this->name = $name;
    }

    public function getVisibility(): bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): void
    {
        $this->recordItemChanged();
        $this->visibility = $visibility;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): void
    {
        $this->recordItemChanged();
        $this->metadata = $metadata;
    }

    public function unassignFromMenu(): void
    {
        $this->menu = null;
    }

    public function assignToMenu(Menu $menu): void
    {
        if ($menu->hasItem($this) === false) {
            throw new \RuntimeException('Cannot assign Item to Menu outside the Menu entity, using assignToMenu() method. Please use Menu->addItem() method to properly add Item to Menu.');
        }

        $this->menu = $menu;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
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

    private function recordItemChanged(): void
    {
        if ($this->menu) {
            $this->menu->recordItemChanged($this);
        }
    }
}
