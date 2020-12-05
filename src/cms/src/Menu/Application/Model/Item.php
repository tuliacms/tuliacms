<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Model;

use Tulia\Cms\Menu\Application\Query\Finder\Model\Item as QueryModelItem;

/**
 * @author Adam Banaszkiewicz
 */
class Item
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var null|string
     */
    protected $menuId;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var null|string
     */
    protected $parentId;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @var null|string
     */
    protected $identity;

    /**
     * @var null|string
     */
    protected $hash;

    /**
     * @var null|string
     */
    protected $target;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $visibility;

    /**
     * @var array
     */
    protected $metadata = [];

    public static function fromQueryModel(QueryModelItem $item): self
    {
        $self = new self($item->getId());
        $self->setId($item->getId());
        $self->setMenuId($item->getMenuId());
        $self->setPosition($item->getPosition());
        $self->setParentId($item->getParentId());
        $self->setType($item->getType());
        $self->setIdentity($item->getIdentity());
        $self->setHash($item->getHash());
        $self->setTarget($item->getTarget());
        $self->setLocale($item->getLocale());
        $self->setName($item->getName());
        $self->setVisibility((bool) $item->getVisibility());
        $self->setMetadata($item->getMetadata()->all());

        return $self;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->metadata[$name] ?? null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value): void
    {
        $this->metadata[$name] = $value;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name): bool
    {
        return true;
    }

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getMenuId(): ?string
    {
        return $this->menuId;
    }

    /**
     * @param string|null $menuId
     */
    public function setMenuId(?string $menuId): void
    {
        $this->menuId = $menuId;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string|null
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * @param string|null $parentId
     */
    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getIdentity(): ?string
    {
        return $this->identity;
    }

    /**
     * @param string|null $identity
     */
    public function setIdentity(?string $identity): void
    {
        $this->identity = $identity;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @param string|null $hash
     */
    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string|null $target
     */
    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function getVisibility(): bool
    {
        return $this->visibility;
    }

    /**
     * @param bool $visibility
     */
    public function setVisibility(bool $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
