<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\ReadModel\Finder\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Menu
{
    protected string $id;
    protected string $name;
    protected string $websiteId;
    protected array $items = [];

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
            $menu->items[] = Item::buildFromArray($item);
        }

        return $menu;
    }

    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getWebsiteId(): ?string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(?string $websiteId): void
    {
        $this->websiteId = $websiteId;
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
}
