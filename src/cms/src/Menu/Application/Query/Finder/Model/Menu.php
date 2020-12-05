<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Menu
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $websiteId;

    /**
     * @var ItemCollection
     */
    protected $items;

    /**
     * @param array $data
     *
     * @return Menu
     */
    public static function buildFromArray(array $data): Menu
    {
        if (isset($data['id']) === false) {
            throw new \InvalidArgumentException('Menu ID must be provided.');
        }

        $menu = new self();
        $menu->setId($data['id']);
        $menu->setName($data['name'] ?? '');
        $menu->setWebsiteId($data['website_id'] ?? null);

        foreach ($data['items'] ?? [] as $item) {
            $menu->addItem(Item::buildFromArray($item));
        }

        return $menu;
    }

    public function __construct()
    {
        $this->items = new ItemCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId(): ?string
    {
        return $this->websiteId;
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId(?string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    /**
     * @return ItemCollection|Item[]
     */
    public function getItems(): ItemCollection
    {
        return $this->items;
    }

    /**
     * @param ItemCollection $items
     */
    public function setItems(ItemCollection $items): void
    {
        $this->items = $items;
    }

    /**
     * @param Item $item
     */
    public function addItem(Item $item): void
    {
        $this->items->append($item);
    }

    /**
     * @param Item $item
     */
    public function removeItem(Item $item): void
    {
        $this->items->remove($item);
    }
}
