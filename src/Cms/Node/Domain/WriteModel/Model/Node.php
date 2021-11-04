<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\AttributeInfo;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\NodeId;
use Tulia\Cms\Platform\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AggregateRoot
{
    protected NodeId $id;

    protected string $type;

    protected string $status = 'draft';

    protected string $websiteId;

    protected ImmutableDateTime $publishedAt;

    protected ?ImmutableDateTime $publishedTo = null;

    protected ImmutableDateTime $createdAt;

    protected ?ImmutableDateTime $updatedAt = null;

    protected ?string $authorId = null;

    protected ?string $parentId = null;

    protected int $level = 0;

    //protected ?string $categoryId = null;

    protected string $locale;

    protected bool $translated = true;

    protected array $attributes = [];

    /**
     * @var AttributeInfo[]
     */
    protected array $attributesInfo = [];

    private function __construct(string $id, string $type, string $websiteId, string $locale)
    {
        $this->id = new NodeId($id);
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
        $this->createdAt = $this->updatedAt = new ImmutableDateTime();
        $this->updatedAt = new ImmutableDateTime();
        $this->publishedAt = new ImmutableDateTime();
    }

    public static function createNew(string $id, string $type, string $websiteId, string $locale): self
    {
        $self = new self($id, $type, $websiteId, $locale);
        $self->recordThat(new Event\NodeCreated($id, $websiteId, $locale, $type));

        return $self;
    }

    public static function buildFromArray(string $nodeType, array $data): self
    {
        $self = new self(
            $data['id'],
            $nodeType,
            $data['website_id'],
            $data['locale']
        );
        $self->status = $data['status'] ?? 'published';
        $self->createdAt = $data['created_at'] ?? new ImmutableDateTime();
        $self->updatedAt = $data['updated_at'] ?? null;
        $self->publishedAt = $data['published_at'] ?? new ImmutableDateTime();
        $self->publishedTo = $data['published_to'] ?? null;
        $self->authorId = $data['author_id'] ?? null;
        $self->parentId = $data['parent_id'] ?? null;
        $self->level = (int) ($data['level'] ?? 0);
        //$self->categoryId = $data['category'] ?? null;
        $self->translated = (bool) ($data['translated'] ?? true);

        foreach ($data['attributes_mapping'] as $name => $info) {
            $self->attributesInfo[$name] = new AttributeInfo(
                $info['multilingual'],
                $info['multiple'],
                $info['compilable'],
                $info['is_slug'],
                $info['is_title'],
            );
        }

        foreach ($data['attributes'] as $name => $value) {
            if (isset($self->attributesInfo[$name]) === false) {
                continue;
            }

            $self->attributes[$name] = $value;
        }

        return $self;
    }

    public function getId(): NodeId
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAttributeInfo(string $name): AttributeInfo
    {
        return $this->attributesInfo[$name];
    }

    public function addAttributeInfo(string $name, AttributeInfo $info): void
    {
        $this->validateAttributeName($name);

        $this->attributesInfo[$name] = $info;
    }

    public function updateAttributes(array $attributes): void
    {
        unset(
            $attributes['id'],
            $attributes['type'],
            $attributes['status']
        );

        foreach ($attributes as $name => $value) {
            if (isset($this->attributesInfo[$name]) === false) {
                throw new \Exception(sprintf('Attribute "%s" Must have AttributeInfo for this attribute.', $name));
            }

            $this->attributes[$name] = $value;
        }

        $this->markAsUpdated();
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function removeAttribute(string $code): void
    {
        unset($this->attributes[$code]);

        $this->markAsUpdated();
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function hasFlag(string $flag): bool
    {
        return isset($this->attributes['flags']) && \in_array($flag, $this->attributes['flags'], true);
    }

    public function getFlags(): array
    {
        return $this->attributes['flags'] ?? [];
    }








    private function updateFlags(array $flags): void
    {
        $this->attributes['flags'] = $this->attributes['flags'] ?? [];

        /*$oldFlags = array_diff($this->attributes['flags'], $flags);
        $newFlags = array_diff($flags, $this->attributes['flags']);*/
    }

    /**
     * @param mixed $code
     */
    private function validateAttributeName($code): void
    {
        if (is_string($code) === false) {
            throw new \Exception('Must be string.');
        }
        if (strlen($code) < 2) {
            throw new \Exception('Must have more than 2 chars.');
        }
        if (! preg_match('/^([a-z0-9_]+)$/m', $code)) {
            throw new \Exception('Must contains only alphanumericals and underscores.');
        }
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new ImmutableDateTime();
    }


    #######################

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
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

    public function getUpdatedAt(): ?ImmutableDateTime
    {
        return $this->updatedAt;
    }

    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    public function setAuthorId(?string $authorId): void
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

    /*public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }*/

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTitle(): ?string
    {
        foreach ($this->attributesInfo as $name => $info) {
            if ($info->isTitle()) {
                return $this->attributes[$name] ?? null;
            }
        }

        return null;
    }

    public function getSlug(): ?string
    {
        foreach ($this->attributesInfo as $name => $info) {
            if ($info->isSlug()) {
                return $this->attributes[$name] ?? null;
            }
        }

        return null;
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    public function setTranslated(bool $translated): void
    {
        $this->translated = $translated;
    }
}
