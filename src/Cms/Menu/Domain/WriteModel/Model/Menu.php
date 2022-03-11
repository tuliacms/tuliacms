<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Model;

use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemAdded;
use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemRemoved;
use Tulia\Cms\Menu\Domain\WriteModel\Event\ItemUpdated;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\CannotModifyRootItemException;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\ParentItemReccurencyException;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
final class Menu extends AggregateRoot
{
    protected string $id;
    protected string $websiteId;
    protected string $locale;
    protected ?string $name = null;
    protected array $itemsChanges = [];
    /** @var Item[] */
    protected array $items = [];

    private function __construct(string $id, string $websiteId, string $locale)
    {
        $this->id = $id;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public static function create(string $id, string $websiteId, string $locale): self
    {
        $menu = new self($id, $websiteId, $locale);
        $root = Item::createRoot($locale, $menu);
        $menu->items[$root->getId()] = $root;
        $menu->recordItemChange('add', $root);

        return $menu;
    }

    public static function buildFromArray(array $data): self
    {
        $menu = new self($data['id'], $data['website_id'], $data['locale']);
        $menu->name = $data['name'] ?? null;

        foreach ($data['items'] as $item) {
            $item['menu'] = $menu;
            $menu->items[$item['id']] = Item::buildFromArray($item);
        }

        return $menu;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'website_id' => $this->websiteId,
            'locale' => $this->locale,
            'items' => $this->itemsToArray(),
        ];
    }

    public function itemsToArray(): array
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[$item->getId()] = $item->toArray();
        }

        return $items;
    }

    public function itemToArray(string $id): array
    {
        return $this->items[$id]->toArray();
    }

    public function collectDomainEvents(): array
    {
        $changes = $this->itemsChanges;
        $this->itemsChanges = [];

        foreach ($changes as $change) {
            if ($change['type'] === 'add') {
                $this->recordThat(new ItemAdded($this->id, $change['item']->getId()));
            } elseif ($change['type'] === 'remove') {
                $this->recordThat(new ItemRemoved($this->id, $change['item']->getId()));
            } else {
                $this->recordThat(new ItemUpdated($this->id, $change['item']->getId()));
            }
        }

        return parent::collectDomainEvents();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function rename(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function createNewItem(string $id): Item
    {
        $item = Item::create($id, $this->locale, $this);
        $item->setParentId(Item::ROOT_ID);

        $this->items[$item->getId()] = $item;

        if ($item->isRoot() === false) {
            $this->resolveItemParent($item);
            $this->calculateItemLevel($item);
            $this->calculateItemPosition($item);
        }

        $this->recordItemChange('add', $item);

        return $item;
    }

    public function updateItemUsingAttributes(string $itemId, array $data, ?array $attributes = null): void
    {
        $item = $this->items[$itemId];

        $item->setName($data['name']);
        $item->setType($data['type']);
        $item->setVisibility($data['visibility'] ? true : false);
        $item->setIdentity($data['identity']);
        $item->setHash($data['hash']);
        $item->setTarget($data['target']);

        if ($attributes !== null) {
            $item->updateAttributes($attributes);
        }

        $this->recordItemChange('update', $item);
    }

    /**
     * @throws CannotModifyRootItemException
     */
    public function removeItem(string $itemId): void
    {
        if (isset($this->items[$itemId]) === false) {
            return;
        }

        $item = $this->items[$itemId];

        if ($item->isRoot()) {
            throw new CannotModifyRootItemException('Cannot remove Root item.');
        }

        $this->removeItemChildren($item);

        unset($this->items[$item->getId()]);

        $this->recordItemChange('remove', $item);
    }

    public function hasItem(string $id): bool
    {
        return isset($this->items[$id]);
    }

    /**
     * @throws ParentItemReccurencyException
     */
    public function updateHierarchy(array $hierarchy): void
    {
        $rebuildedHierarchy = [];

        foreach ($hierarchy as $child => $parent) {
            $rebuildedHierarchy[$parent][] = $child;
        }

        foreach ($rebuildedHierarchy as $parent => $items) {
            foreach ($items as $level => $id) {
                $item = $this->items[$id];
                $item->setParentId($parent ?: Item::ROOT_ID);
                $this->detectParentReccurency($item);
                $item->setPosition($level + 1);
                $this->recordItemChange('update', $item);
            }
        }

        $this->calculateLevel(Item::ROOT_ID, Item::ROOT_LEVEL);
    }

    private function calculateLevel(string $parentId, int $baseLevel): void
    {
        foreach ($this->items as $item) {
            if ($item->getParentId() === $parentId) {
                $item->setLevel($baseLevel + 1);
                $this->recordItemChange('update', $item);
                $this->calculateLevel($item->getId(), $baseLevel + 1);
            }
        }
    }

    /**
     * @throws ParentItemReccurencyException
     */
    private function detectParentReccurency(Item $item): void
    {
        // @todo Implement recurrency detection.
    }

    private function calculateItemLevel(Item $item): void
    {
        $parent = $this->items[$item->getParentId()];
        $item->setLevel($parent->getLevel() + 1);
    }

    private function calculateItemPosition(Item $item): void
    {
        if ($item->getPosition() === 0) {
            $position = 0;

            foreach ($this->items as $existingItem) {
                if ($existingItem->getParentId() === $item->getParentId()) {
                    $position = max($position, $existingItem->getPosition());
                }
            }

            $item->setPosition($position + 1);
        }
    }

    private function resolveItemParent(Item $item): void
    {
        if ($item->getParentId() === null) {
            $item->setParentId(Item::ROOT_ID);
        }
    }

    private function removeItemChildren(Item $item): void
    {
        foreach ($this->items as $existingItem) {
            if ($existingItem->getParentId() === null) {
                continue;
            }

            if ($existingItem->getParentId() === $item->getId()) {
                $this->removeItem($existingItem->getId());
            }
        }
    }

    private function recordItemChange(string $type, Item $item): void
    {
        // Prevents multiple do the same change with the same item.
        foreach ($this->itemsChanges as $key => $change) {
            if ($change['type'] === $type && $change['item']->getId() === $item->getId()) {
                unset($this->itemsChanges[$key]);
            }
        }

        if ($type === 'update') {
            // If item has beed added or removed already, we don't add any 'update' changes.
            foreach ($this->itemsChanges as $change) {
                if ($change['item']->getId() === $item->getId() && \in_array($change['type'], ['add', 'remove'])) {
                    return;
                }
            }
        } elseif ($type === 'add' || $type === 'remove') {
            // If item has beed added or removed, we remove all the 'update' changes.
            foreach ($this->itemsChanges as $key => $change) {
                if ($change['item']->getId() === $item->getId() && $change['type'] === 'update') {
                    unset($this->itemsChanges[$key]);
                }
            }
        }

        $this->itemsChanges[] = ['type' => $type, 'item' => $item];
    }
}
