<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Menu\Model\Aggregate;

use Tulia\Cms\Menu\Domain\Menu\Exception\ParentItemReccurencyException;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\ItemId;

/**
 * @author Adam Banaszkiewicz
 */
class Item
{
    /**
     * @var ItemId
     */
    protected $id;

    /**
     * @var null|Menu
     */
    protected $menu;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var null|Item
     */
    protected $parent;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @var null|string
     */
    protected $identity;

    /**
     * @var null|string
     */
    protected $hash;

    /**
     * @var null|string
     */
    protected $target;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $visibility;

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @param ItemId $id
     * @param string $locale
     */
    public function __construct(ItemId $id, string $locale)
    {
        $this->id     = $id;
        $this->locale = $locale;
    }

    /**
     * @return ItemId
     */
    public function getId(): ItemId
    {
        return $this->id;
    }

    /**
     * @return null|Item
     */
    public function getParent(): ?Item
    {
        return $this->parent;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function changeMetadataValue(string $name, $value): void
    {
        if (\array_key_exists($name, $this->metadata)) {
            if ($this->metadata[$name] !== $value) {
                $this->recordChangeEvent();

                $this->metadata[$name] = $value;
            }
        } else {
            if (empty($value) === false) {
                $this->recordChangeEvent();

                $this->metadata[$name] = $value;
            }
        }
    }

    /**
     * @param null|string $name
     */
    public function rename(?string $name): void
    {
        if ($this->name !== $name) {
            $this->name = $name;
            $this->recordChangeEvent();
        }
    }

    /**
     * @param Menu $menu
     */
    public function assignToMenu(Menu $menu): void
    {
        $this->menu = $menu;
    }

    public function unassignFromMenu(): void
    {
        $this->menu = null;
    }

    /**
     * @param null|Item $parent
     *
     * @throws ParentItemReccurencyException
     */
    public function assignToParent(?Item $parent): void
    {
        if ($this->parent !== $parent) {
            if ($parent instanceof self) {
                $this->detectParentReccurency($parent);
                $this->level = $parent->level + 1;
            }

            $this->parent = $parent;
            $this->recordChangeEvent();
        }
    }

    /**
     * @param bool $visibility
     */
    public function changeVisibility(bool $visibility): void
    {
        if ($this->visibility !== $visibility) {
            $this->visibility = $visibility;
            $this->recordChangeEvent();
        }
    }

    /**
     * @param int $position
     */
    public function moveToPosition(int $position): void
    {
        if ($this->position !== $position) {
            $this->position = $position;
            $this->recordChangeEvent();
        }
    }

    /**
     * @param string|null $type
     */
    public function changeType(?string $type): void
    {
        if ($this->type !== $type) {
            $this->type = $type;
            $this->recordChangeEvent();
        }
    }

    /**
     * @param string|null $identity
     */
    public function changeIdentity(?string $identity): void
    {
        if ($this->identity !== $identity) {
            $this->identity = $identity;
            $this->recordChangeEvent();
        }
    }

    /**
     * @param string|null $hash
     */
    public function changeHash(?string $hash): void
    {
        if ($this->hash !== $hash) {
            $this->hash = $hash;
            $this->recordChangeEvent();
        }
    }

    /**
     * @param string|null $target
     */
    public function changeTarget(?string $target): void
    {
        if ($this->target !== $target) {
            $this->target = $target;
            $this->recordChangeEvent();
        }
    }

    private function recordChangeEvent(): void
    {
        if ($this->menu) {
            $this->menu->recordItemChanged($this);
        }
    }

    /**
     * @param Item $item
     *
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
