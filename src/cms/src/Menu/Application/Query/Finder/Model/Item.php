<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder\Model;

use InvalidArgumentException;
use Tulia\Cms\Metadata\MagickMetadataTrait;
use Tulia\Cms\Metadata\Metadata;
use Tulia\Cms\Metadata\MetadataTrait;

/**
 * @author Adam Banaszkiewicz
 */
class Item
{
    use MagickMetadataTrait;
    use MetadataTrait;

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

    protected static $fields = [
        'id'         => 'id',
        'menu_id'    => 'menuId',
        'position'   => 'position',
        'parent_id'  => 'parentId',
        'level'      => 'level',
        'type'       => 'type',
        'identity'   => 'identity',
        'hash'       => 'hash',
        'target'     => 'target',
        'locale'     => 'locale',
        'name'       => 'name',
        'visibility' => 'visibility',
        'translated' => 'translated',
    ];

    /**
     * @param array $data
     *
     * @return Item
     */
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

        $item->setMetadata(new Metadata($data['metadata'] ?? []));

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $params = []): array
    {
        $params = array_merge([
            'skip' => [],
        ], $params);

        $result = [];

        foreach (static::$fields as $key => $property) {
            $result[$key] = $this->{$property};
        }

        foreach ($params['skip'] as $skip) {
            unset($result[$skip]);
        }

        return $result;
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
    public function getMenuId(): ?string
    {
        return $this->menuId;
    }

    /**
     * {@inheritdoc}
     */
    public function setMenuId(?string $menuId): void
    {
        $this->menuId = $menuId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($level): void
    {
        $this->level = $level;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity(): ?string
    {
        return $this->identity;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentity(?string $identity): void
    {
        $this->identity = $identity;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
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
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibility(): int
    {
        return $this->visibility;
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility(int $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @return mixed
     */
    public function getTranslated()
    {
        return $this->translated;
    }

    /**
     * @param mixed $translated
     */
    public function setTranslated($translated): void
    {
        $this->translated = $translated;
    }
}
