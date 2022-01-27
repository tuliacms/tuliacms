<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Model;

use InvalidArgumentException;
use Tulia\Cms\Node\Infrastructure\Domain\ReadModel\NodeContent\VoidNodeContent;
use Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Node implements \ArrayAccess
{
    protected string $id;
    protected string $type;
    protected string $status;
    protected string $websiteId;
    protected ImmutableDateTime $publishedAt;
    protected ?ImmutableDateTime $publishedTo;
    protected ImmutableDateTime $createdAt;
    protected ?ImmutableDateTime $updatedAt;
    protected string $authorId;
    protected ?string $parentId;
    protected int $level;
    //protected $category;
    protected string $locale;
    protected ?string $title;
    protected ?string $slug;
    protected bool $visibility;
    protected array $flags = [];
    protected array $attributes = [];

    public function __get(string $name)
    {
        return $this->{$name} ?? $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        if (method_exists($this, $name)) {
            $this->{$name} = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        return $this->attributes[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return method_exists($this, $name) || isset($this->attributes[$name]);
    }

    public static function buildFromArray(array $data): self
    {
        $node = new self();

        if (isset($data['id']) === false) {
            throw new InvalidArgumentException('Node ID must be provided.');
        }

        if (isset($data['website_id']) === false) {
            throw new InvalidArgumentException('Node website_id must be provided.');
        }

        if (isset($data['locale']) === false) {
            $data['locale'] = 'en_US';
        }

        $data = static::setDatetime($data, 'published_at', new ImmutableDateTime());
        $data = static::setDatetime($data, 'published_to');
        $data = static::setDatetime($data, 'created_at', new ImmutableDateTime());
        $data = static::setDatetime($data, 'updated_at');

        $node->id = $data['id'];
        $node->type = $data['type'] ?? 'page';
        $node->setStatus($data['status'] ?? 'published');
        $node->setWebsiteId($data['website_id']);
        $node->setPublishedAt($data['published_at']);
        $node->setPublishedTo($data['published_to']);
        $node->setCreatedAt($data['created_at']);
        $node->setUpdatedAt($data['updated_at']);
        $node->setAuthorId($data['author_id'] ?? null);
        $node->setParentId($data['parent_id'] ?? null);
        $node->setLevel((int) ($data['level'] ?? 0));
        //$node->setCategory($data['category'] ?? null);
        $node->setLocale($data['locale']);
        $node->setTitle($data['title'] ?? '');
        $node->setSlug($data['slug'] ?? '');
        $node->setFlags($data['flags'] ?? []);
        $node->attributes = $data['attributes'];

        foreach ($data['attributes_info'] as $name => $info) {
            if ($info['compilable']) {
                $node->attributes[$name] = new VoidNodeContent($node->attributes[$name]);
            }
        }

        return $node;
    }

    private static function setDatetime(array $data, string $key, $default = null): array
    {
        if (\array_key_exists($key, $data) === false) {
            $data[$key] = $default;
        } elseif ($data[$key] === null && $default === null) {
            // Do nothing, allow to null;
        } elseif ($data[$key] instanceof ImmutableDateTime === false) {
            $data[$key] = new ImmutableDateTime($data[$key]);
        }

        return $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getWebsiteId(): ?string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(?string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    public function getPublishedAt(): ImmutableDateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(ImmutableDateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getPublishedTo(): ?ImmutableDateTime
    {
        return $this->publishedTo;
    }

    public function setPublishedTo(?ImmutableDateTime $publishedTo): void
    {
        $this->publishedTo = $publishedTo;
    }

    public function getCreatedAt(): ImmutableDateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(ImmutableDateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?ImmutableDateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?ImmutableDateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    public function setAuthorId(string $authorId): void
    {
        $this->authorId = $authorId;
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function hasFlag(string $flag): bool
    {
        return in_array($flag, $this->flags);
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function setFlags(array $flags): void
    {
        $this->flags = $flags;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->attributes);
    }

    public function offsetGet($offset)
    {
        if (isset($this->attributes[$offset])) {
            return $this->attributes[$offset];
        }

        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
