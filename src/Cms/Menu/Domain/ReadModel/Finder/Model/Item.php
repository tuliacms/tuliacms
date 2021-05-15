<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\ReadModel\Finder\Model;

use InvalidArgumentException;
use Tulia\Cms\Metadata\Domain\ReadModel\MagickMetadataTrait;

/**
 * @author Adam Banaszkiewicz
 */
class Item
{
    use MagickMetadataTrait;

    protected $id;
    protected $menuId;
    protected $position;
    protected $parentId;
    protected $level;
    protected $type;
    protected $identity;
    protected $hash;
    protected $target;
    protected $locale;
    protected $name;
    protected $visibility;
    protected $translated;

    public static function buildFromArray(array $data): Item
    {
        if (isset($data['id']) === false) {
            throw new InvalidArgumentException('Menu Item ID must be provided.');
        }

        $item = new self();
        $item->setId($data['id']);
        $item->setMenuId($data['menu_id'] ?? null);
        $item->setPosition((int) ($data['position'] ?? 0));
        $item->setParentId($data['parent_id'] ?? null);
        $item->setLevel((int) ($data['level'] ?? 0));
        $item->setType($data['type'] ?? '');
        $item->setIdentity($data['identity'] ?? '');
        $item->setHash($data['hash'] ?? '');
        $item->setTarget($data['target'] ?? '');
        $item->setName($data['name'] ?? '');
        $item->setLocale($data['locale'] ?? 'en_US');
        $item->setVisibility((int) ($data['visibility'] ?? 1));
        $item->setTranslated((bool) ($data['translated'] ?? true));

        $item->metadata = $data['metadata'] ?? [];

        return $item;
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

    public function getMenuId(): ?string
    {
        return $this->menuId;
    }

    public function setMenuId(?string $menuId): void
    {
        $this->menuId = $menuId;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getVisibility(): int
    {
        return $this->visibility;
    }

    public function setVisibility(int $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getTranslated()
    {
        return $this->translated;
    }

    public function setTranslated($translated): void
    {
        $this->translated = $translated;
    }
}
