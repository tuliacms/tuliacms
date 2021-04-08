<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Menu\Model\Aggregate;

use Tulia\Cms\Menu\Domain\Menu\Exception\ItemNotFoundException;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;
use Tulia\Cms\Menu\Domain\Menu\Event;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\ItemId;
use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
class Menu extends AggregateRoot
{
    /**
     * @var AggregateId
     */
    protected $id;

    /**
     * @var string
     */
    protected $websiteId;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var Item[]
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $itemsChanges = [];

    /**
     * @param AggregateId $id
     * @param string $websiteId
     */
    public function __construct(AggregateId $id, string $websiteId)
    {
        $this->id        = $id;
        $this->websiteId = $websiteId;

        $this->recordThat(new Event\MenuCreated($id, $websiteId));
    }

    public static function reconstruct(array $data): self
    {
        $self = new self(new AggregateId($data['id']), $data['website_id']);
        $self->name = $data['name'];

        foreach ($data['items'] as $item) {
            $self->addItem($item);
        }

        $self->collectDomainEvents();
        $self->itemsChanges = [];

        return $self;
    }

    /**
     * @return AggregateId
     */
    public function getId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getItemsChanges(): array
    {
        $changes = $this->itemsChanges;
        $this->itemsChanges = [];
        return $changes;
    }

    /**
     * @param null|string $name
     */
    public function rename(?string $name): void
    {
        if ($this->name !== $name) {
            $this->name = $name;
            $this->recordThat(new Event\Renamed($this->id, $name));
        }
    }

    /**
     * @param Item $item
     */
    public function addItem(Item $item): void
    {
        if (isset($this->items[$item->getId()->getId()])) {
            return;
        }

        $this->items[$item->getId()->getId()] = $item;
        $item->assignToMenu($this);

        $this->recordThat(new Event\ItemAdded($this->id, $item->getId()));
        $this->recordItemChange('add', $item->getId()->getId());
    }

    /**
     * @param Item $item
     */
    public function removeItem(Item $item): void
    {
        $position = array_search($item, $this->items);

        if ($position !== false) {
            $this->items[$position]->unassignFromMenu();

            unset($this->items[$position]);

            $this->recordThat(new Event\ItemRemoved($this->id, $item->getId()));
            $this->recordItemChange('remove', $item->getId()->getId());
        }
    }

    /**
     * @param ItemId $id
     *
     * @return Item
     *
     * @throws ItemNotFoundException
     */
    public function getItem(ItemId $id): Item
    {
        if (isset($this->items[$id->getId()]) === false) {
            throw new ItemNotFoundException(sprintf('Item with ID %s not found.', $id->getId()));
        }

        return $this->items[$id->getId()];
    }

    /**
     * @param Item $item
     */
    public function recordItemChanged(Item $item): void
    {
        $this->recordThat(new Event\ItemUpdated($this->id, $item->getId()));
        $this->recordItemChange('update', $item->getId()->getId());
    }

    /**
     * @param string $type
     * @param string $id
     */
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
