<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Model;

use Tulia\Cms\Menu\Domain\WriteModel\Exception\CannotModifyRootItemException;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\ItemNotFoundException;
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function getItemsChanges(): array
    {
        $changes = $this->itemsChanges;
        $this->itemsChanges = [];
        return $changes;
    }

    /**
     * @return Item[]
     */
    public function items(): iterable
    {
        foreach ($this->items as $item) {
            yield $item;
        }
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

    /**
     * @throws CannotModifyRootItemException
     */
    public function removeItem(Item $item): void
    {
        if ($item->isRoot()) {
            throw new CannotModifyRootItemException('Cannot remove Root item.');
        }
        if (isset($this->items[$item->getId()]) === false) {
            return;
        }

        $this->removeItemChildren($item);

        $this->items[$item->getId()]->unassignFromMenu();

        unset($this->items[$item->getId()]);

        $this->recordItemChange('remove', $item);
    }

    public function hasItem(Item $item): bool
    {
        return isset($this->items[$item->getId()]);
    }

    /**
     * @throws ItemNotFoundException
     * @throws CannotModifyRootItemException
     */
    public function getItem(string $id): Item
    {
        if (isset($this->items[$id]) === false) {
            throw new ItemNotFoundException(sprintf('Item with ID %s not found.', $id));
        }
        if ($this->items[$id]->isRoot()) {
            throw new CannotModifyRootItemException('Cannot modify Root item.');
        }

        return $this->items[$id];
    }

    public function recordItemChanged(Item $item): void
    {
        $this->recordItemChange('update', $item);
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
                $this->removeItem($existingItem);
            }
        }
    }
}
