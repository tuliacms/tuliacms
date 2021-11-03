<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\FieldValue;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\NodeId;
use Tulia\Cms\Platform\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AggregateRoot
{
    protected NodeId $id;

    protected NodeType $type;

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

    private function __construct(string $id, NodeType $type, string $websiteId, string $locale)
    {
        $this->id = new NodeId($id);
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
        $this->createdAt = $this->updatedAt = new ImmutableDateTime();
        $this->updatedAt = new ImmutableDateTime();
        $this->publishedAt = new ImmutableDateTime();
    }

    public static function createNew(string $id, NodeType $type, string $websiteId, string $locale): self
    {
        $self = new self($id, $type, $websiteId, $locale);
        $self->recordThat(new Event\NodeCreated($id, $websiteId, $locale, $type->getType()));

        return $self;
    }

    public static function buildFromArray(NodeType $nodeType, array $data): self
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

        unset(
            $data['id'],
            $data['website_id'],
            $data['locale'],
            $data['status'],
            $data['translated'],
            //$data['category'],
            $data['level'],
            $data['author_id'],
            $data['parent_id'],
            $data['created_at'],
            $data['updated_at'],
            $data['published_at'],
            $data['published_to'],
        );

        foreach ($data as $key => $value) {
            if ($nodeType->hasField($key) === false) {
                continue;
            }

            $field = $nodeType->getField($key);

            $self->attributes[$field->getName()] = new FieldValue(
                $value,
                $field->isMultiple(),
                $field->isMultilingual()
            );
        }

        return $self;
    }

    public function getId(): NodeId
    {
        return $this->id;
    }

    public function getType(): NodeType
    {
        return $this->type;
    }

    public function getAttributes(): array
    {
        return array_combine(
            array_keys($this->attributes),
            array_map(static function ($value) {
                return $value->getValue();
            }, $this->attributes)
        );
    }

    /**
     * @param FieldValue[] $attributes
     */
    public function updateAttributes(array $attributes): void
    {
        unset(
            $attributes['id'],
            $attributes['type'],
            $attributes['status']
        );

        foreach ($attributes as $name => $attribute) {
            $this->validateAttributeName($name);
            $this->validateAttributeClassname($attribute);

            if ($name === 'flags') {
                $this->updateFlags($attribute->getValue());
            }

            $this->attributes[$name] = $attribute;
        }

        $this->markAsUpdated();
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
        return isset($this->attributes['flags']) && \in_array($flag, $this->attributes['flags']->getValue(), true);
    }

    public function getFlags(): array
    {
        return $this->attributes['flags']->getValue() ?? [];
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
            echo 'Must be string.';exit;
        }
        if (strlen($code) < 2) {
            echo 'Must have more than 2 chars.';exit;
        }
        if (! preg_match('/^([a-z0-9_]+)$/m', $code)) {
            echo 'Must contains only alphanumericals and underscores.';exit;
        }
    }

    private function validateAttributeClassname($attribute): void
    {
        if (! $attribute instanceof FieldValue) {
            echo 'Must be instance of ' . FieldValue::class;exit;
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
        $field = $this->type->getTitleField();


        if ($field === null || isset($this->attributes[$field->getName()]) === false) {
            return null;
        }

        return $this->attributes[$field->getName()]->getValue();
    }

    public function getSlug(): ?string
    {
        $field = $this->type->getSlugField();

        if ($field === null || isset($this->attributes[$field->getName()]) === false) {
            return null;
        }

        return $this->attributes[$field->getName()]->getValue();
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
