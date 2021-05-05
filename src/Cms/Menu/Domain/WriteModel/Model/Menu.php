<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Model;

use Tulia\Cms\Menu\Domain\WriteModel\Exception\ItemNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class Menu
{
    protected string $id;
    protected string $websiteId;
    protected ?string $name = null;
    protected array $itemsChanges = [];

    /**
     * @var Item[]
     */
    protected array $items = [];

    public function __construct(string $id, string $websiteId)
    {
        $this->id = $id;
        $this->websiteId = $websiteId;
    }

    public static function buildFromArray(array $data): self
    {
        $menu = new self($data['id'], $data['website_id']);
        $menu->name = $data['name'] ?? null;

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

    public function addItem(Item $item): void
    {
        if (isset($this->items[$item->getId()])) {
            return;
        }

        $this->items[$item->getId()] = $item;
        $item->assignToMenu($this);

        $this->recordItemChange('add', $item->getId());
    }

    public function removeItem(Item $item): void
    {
        $position = array_search($item, $this->items, true);

        if ($position !== false) {
            $this->items[$position]->unassignFromMenu();

            unset($this->items[$position]);

            $this->recordItemChange('remove', $item->getId());
        }
    }

    public function hasItem(Item $item): bool
    {
        return isset($this->items[$item->getId()]);
    }

    /**
     * @param string $id
     * @return Item
     * @throws ItemNotFoundException
     */
    public function getItem(string $id): Item
    {
        if (isset($this->items[$id]) === false) {
            throw new ItemNotFoundException(sprintf('Item with ID %s not found.', $id));
        }

        return $this->items[$id];
    }

    public function recordItemChanged(Item $item): void
    {
        $this->recordItemChange('update', $item->getId());
    }

    private function recordItemChange(string $type, string $id): void
    {
        // Prevents multiple do the same change with the same item.
        foreach ($this->itemsChanges as $key => $change) {
            if ($change['type'] === $type && $change['id'] === $id) {
                unset($this->itemsChanges[$key]);
            }
        }

        if ($type === 'update') {
            // If item has beed added or removed already, we don't add any 'update' changes.
            foreach ($this->itemsChanges as $change) {
                if ($change['id'] === $id && \in_array($change['type'], ['add', 'remove'])) {
                    return;
                }
            }
        } elseif ($type === 'add' || $type === 'remove') {
            // If item has beed added or removed, we remove all the 'update' changes.
            foreach ($this->itemsChanges as $key => $change) {
                if ($change['id'] === $id && $change['type'] === 'update') {
                    unset($this->itemsChanges[$key]);
                }
            }
        }

        $this->itemsChanges[] = ['type' => $type, 'id' => $id];
    }
}
