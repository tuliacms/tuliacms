<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Model;

use Tulia\Cms\Attributes\Domain\WriteModel\MagickAttributesTrait;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\AttributesAwareInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Event\AttributeUpdated;

use function is_string;

/**
 * @author Adam Banaszkiewicz
 */
class Item implements AttributesAwareInterface
{
    use MagickAttributesTrait;

    public const ROOT_ID = '00000000-0000-0000-0000-000000000000';
    public const ROOT_LEVEL = 0;

    protected string $id;
    protected Menu $menu;
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

    private function __construct(string $id, string $locale, Menu $menu, bool $isRoot = false)
    {
        $this->id = $id;
        $this->locale = $locale;
        $this->isRoot = $isRoot;
        $this->menu = $menu;
    }

    public static function create(string $id, string $locale, Menu $menu, bool $isRoot = false): self
    {
        return new self($id, $locale, $menu, $isRoot);
    }

    public static function buildFromArray(array $data): self
    {
        $item = new self($data['id'], $data['locale'], $data['menu'], (bool) $data['is_root']);
        $item->name = $data['name'] ?? null;
        $item->setParentId($data['parent_id'] ?? Item::ROOT_ID);
        $item->position = (int) ($data['position'] ?? 0);
        $item->level = (int) ($data['level'] ?? 0);
        $item->type = $data['type'] ?? null;
        $item->identity = $data['identity'] ?? null;
        $item->hash = $data['hash'] ?? null;
        $item->target = $data['target'] ?? null;
        $item->locale = $data['locale'];
        $item->translated = (bool) ($data['translated'] ?? false);
        $item->visibility = (bool) ($data['visibility'] ?? 1);
        $item->attributes = $data['metadata'] ?? [];

        return $item;
    }

    public static function createRoot(string $locale, Menu $menu): self
    {
        $item = new self(self::ROOT_ID, $locale, $menu, true);
        $item->name = 'root';
        $item->parentId = null;
        $item->position = 0;
        $item->level = 0;
        $item->locale = $locale;
        $item->translated = false;
        $item->visibility = true;

        return $item;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parentId,
            'position' => $this->position,
            'level' => $this->level,
            'is_root' => $this->isRoot,
            'type' => $this->type,
            'identity' => $this->identity,
            'hash' => $this->hash,
            'target' => $this->target,
            'locale' => $this->locale,
            'name' => $this->name,
            'visibility' => $this->visibility,
            'translated' => $this->translated,
            'attributes' => $this->attributes,
        ];
    }

    public function getId(): string
    {
        return $this->id;
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
                '/[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-(8|9|a|b)[a-f0-9]{3}-[a-f0-9]{12}/',
                $parentId,
                $m
            )) {
                throw new \InvalidArgumentException(sprintf('ParentID must be an UUID4 format, given "%s" (%s).', $parentId, gettype($parentId)));
            }
        }

        $this->parentId = $parentId;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
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

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
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
        $this->name = $name;
    }

    public function getVisibility(): bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @param Attribute[] $attributes
     */
    public function updateAttributes(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            $name = $attribute->getCode();
            $value = $attribute->getValue();

            if (isset($this->attributes[$name]) === false || $this->attributes[$name]->getValue() !== $value) {
                $this->attributes[$attribute->getUri()] = $attribute;

                $this->recordThat(AttributeUpdated::fromModel($this, $name, $value));
            }
        }
    }
}
