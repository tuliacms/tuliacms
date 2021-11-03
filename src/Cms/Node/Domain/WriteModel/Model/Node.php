<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

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

    protected string $status;

    protected string $websiteId;

    protected ImmutableDateTime $publishedAt;

    protected ?ImmutableDateTime $publishedTo = null;

    protected ImmutableDateTime $createdAt;

    protected ?ImmutableDateTime $updatedAt = null;

    protected ?string $authorId = null;

    protected ?string $parentId = null;

    protected int $level = 0;

    protected ?string $categoryId = null;

    protected string $locale;

    protected bool $translated = true;

    protected array $attributes = [];

    private function __construct(string $id, NodeType $type, string $websiteId, string $locale)
    {
        $this->id = new NodeId($id);
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
        $this->createdAt = new ImmutableDateTime();
        $this->publishedAt = new ImmutableDateTime();
    }

    public static function createNew(string $id, NodeType $type, string $websiteId, string $locale): self
    {
        $self = new self($id, $type, $websiteId, $locale);
        $self->recordThat(new Event\NodeCreated($id, $websiteId, $locale, $type->getType()));

        return $self;
    }

    public static function buildFromArray(array $data): self
    {
        $self = new self(
            $data['id'],
            $data['type'],
            $data['website_id'],
            $data['locale']
        );
        $self->status = $data['status'] ?? 'published';
        $self->createdAt = $data['created_at'] ?? new ImmutableDateTime();
        $self->updatedAt = $data['updated_at'] ?? null;
        $self->publishedAt = $data['published_at'] ?? new ImmutableDateTime();
        $self->publishedTo = $data['published_to'] ?? null;
        $self->authorId = $data['authorId'] ?? null;
        $self->parentId = $data['parentId'] ?? null;
        $self->level = (int) ($data['level'] ?? 0);
        $self->categoryId = $data['category'] ?? null;
        $self->title = $data['title'] ?? null;
        $self->slug = $data['slug'] ?? null;
        $self->introduction = $data['introduction'] ?? null;
        $self->content = $data['content'] ?? null;
        $self->contentCompiled = $data['content_compiled'] ?? null;
        $self->translated = (bool) ($data['translated'] ?? true);
        $self->flags = $data['flags'] ?? [];

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
            array_column($this->attributes, 'value')
        );
    }

    public function updateAttributes(array $data): void
    {
        unset(
            $data['id'],
            $data['type'],
            $data['status']
        );

        foreach ($data as $code => $info) {
            $this->validateDataCode($code);
            $info = $this->validateDataInformations($code, $info);

            if ($code === 'flags') {
                $this->updateFlags($info['value']);
            }

            $this->attributes[$code] = $info;
        }
    }

    public function removeAttribute(string $code): void
    {
        unset($this->attributes[$code]);
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
        return isset($this->attributes['flags']['value']) && \in_array($flag, $this->attributes['flags']['value'], true);
    }

    public function getFlags(): array
    {
        return $this->attributes['flags']['value'] ?? [];
    }








    private function updateFlags(array $flags): void
    {
        $this->attributes['flags'] = $this->attributes['flags'] ?? [];

        $oldFlags = array_diff($this->attributes['flags'], $flags);
        $newFlags = array_diff($flags, $this->attributes['flags']);
    }

    /**
     * @param mixed $code
     */
    private function validateDataCode($code): void
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

    /**
     * @param string $code
     * @param mixed $info
     * @return array
     */
    private function validateDataInformations(string $code, $info): array
    {
        if (isset($info['value'], $info['multiple'], $info['multilingual']) === false) {
            if ($this->type->hasField($code) === false) {
                echo 'Must contains "value", "multilingual" and "multiple" informations, if the field not exists in the NodeType.';exit;
            }

            $field = $this->type->getField($code);

            $info = [
                'value' => $info,
                'multiple' => $field->isMultiple(),
                'multilingual' => $field->isMultilingual(),
            ];
        }

        if (is_bool($info['multiple']) === false) {
            echo 'The "multiple" must be boolean.';exit;
        }

        if (is_bool($info['multilingual']) === false) {
            echo 'The "multilingual" must be boolean.';exit;
        }

        return $info;
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

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
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

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): void
    {
        $this->introduction = $introduction;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getContentCompiled(): ?string
    {
        return $this->contentCompiled;
    }

    public function setContentCompiled(?string $contentCompiled): void
    {
        $this->contentCompiled = $contentCompiled;
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
