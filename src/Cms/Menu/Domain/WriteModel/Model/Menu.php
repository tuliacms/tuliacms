<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Model;

use Tulia\Cms\Menu\Domain\WriteModel\Exception\ItemNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\MenuId;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\ItemId;

/**
 * @author Adam Banaszkiewicz
 */
class Menu
{
    protected MenuId $id;
    protected string $websiteId;
    protected ?string $name = null;
    protected array $itemsChanges = [];

    /**
     * @var Item[]
     */
    protected array $items = [];

    public function __construct(MenuId $id, string $websiteId)
    {
        $this->id = $id;
        $this->websiteId = $websiteId;
    }

    public static function buildFromArray(array $data): self
    {
        $menu = new self(new MenuId($data['id']), $data['website_id']);
        $menu->name = $data['name'] ?? null;

        return $menu;
    }

    public function getId(): MenuId
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
        if (isset($this->items[$item->getId()->getId()])) {
            return;
        }

        $this->items[$item->getId()->getId()] = $item;
        $item->assignToMenu($this);

        $this->recordItemChange('add', $item->getId()->getId());
    }

    public function removeItem(Item $item): void
    {
        $position = array_search($item, $this->items);

        if ($position !== false) {
            $this->items[$position]->unassignFromMenu();

            unset($this->items[$position]);

            $this->recordItemChange('remove', $item->getId()->getId());
        }
    }

    /**
     * @param ItemId $id
     * @return Item
     * @throws ItemNotFoundException
     */
    public function getItem(ItemId $id): Item
    {
        if (isset($this->items[$id->getId()]) === false) {
            throw new ItemNotFoundException(sprintf('Item with ID %s not found.', $id->getId()));
        }

        return $this->items[$id->getId()];
    }

    public function recordItemChanged(Item $item): void
    {
        $this->recordItemChange('update', $item->getId()->getId());
    }

    private function recordItemChange(string $type, string $id): void
    {
        foreach ($this->itemsChanges as $key => $change) {
            if ($change['type'] === $type && $change['id'] === $id) {
                /**
                 * Prevents multiple do the same change with the same item.
                 */
                unset($this->itemsChanges[$key]);
            }
        }

        $this->itemsChanges[] = ['type' => $type, 'id' => $id];
    }
}
