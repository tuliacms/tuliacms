<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Model;

use Tulia\Cms\Menu\Domain\WriteModel\Exception\ParentItemReccurencyException;
use Tulia\Cms\Metadata\Domain\WriteModel\MagickMetadataTrait;
use Tulia\Cms\Metadata\Ports\Domain\WriteModel\MetadataAwareInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Item implements MetadataAwareInterface
{
    use MagickMetadataTrait;

    public const ROOT_ID = '00000000-0000-0000-0000-000000000000';
    public const ROOT_LEVEL = 0;

    protected string $id;

    protected ?Menu $menu = null;

    protected ?string $parentId = null;

    protected int $position = 0;

    protected int $level = 0;

    protected bool $isRoot = false;

    protected ?string $type = null;

    protected ?string $identity = null;

    protected ?string $hash = null;

    protected ?string $target = null;

    protected string $locale = 'en_US';

    protected bool $translated = false;

    protected ?string $name = null;

    protected bool $visibility = true;

    private function __construct(string $id, string $locale, bool $isRoot = false)
    {
        $this->id = $id;
        $this->locale = $locale;
        $this->isRoot = $isRoot;
    }

    public static function create(string $id, string $locale, bool $isRoot = false): self
    {
        return new self($id, $locale, $isRoot);
    }

    public static function buildFromArray(array $data): self
    {
        $item = new self($data['id'], $data['locale'], (bool) $data['is_root']);
        $item->name = $data['name'] ?? null;
        $item->setParentId($data['parent_id'] ?? null);
        $item->position = (int) ($data['position'] ?? 0);
        $item->level = (int) ($data['level'] ?? 0);
        $item->type = $data['type'] ?? null;
        $item->identity = $data['identity'] ?? null;
        $item->hash = $data['hash'] ?? null;
        $item->target = $data['target'] ?? null;
        $item->locale = $data['locale'];
        $item->translated = (bool) ($data['translated'] ?? false);
        $item->visibility = (bool) ($data['visibility'] ?? 1);
        $item->replaceMetadata($data['metadata'] ?? []);

        return $item;
    }

    public static function createRoot(string $locale): self
    {
        $item = new self(self::ROOT_ID, $locale, true);
        $item->name = 'root';
        $item->parentId = null;
        $item->position = 0;
        $item->level = 0;
        $item->locale = $locale;
        $item->translated = false;
        $item->visibility = true;

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
        if ($this->isRoot()) {
            $this->parentId = null;
            return;
        }

        if ($parentId === '') {
            $parentId = null;
        }

        if (is_string($parentId) && $parentId !== self::ROOT_ID) {
            if (! preg_match(
                '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
                $parentId,
                $m
            )) {
                throw new \InvalidArgumentException(sprintf('ParentID must be an UUID4 format, given "%s" (%s).', $parentId, gettype($parentId)));
            }
        }

        $this->parentId = $parentId;
        $this->recordItemChanged();
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

    public function isRoot(): bool
    {
        return $this->isRoot;
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
