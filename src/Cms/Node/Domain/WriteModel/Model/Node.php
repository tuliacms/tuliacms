<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Tulia\Cms\Metadata\Domain\WriteModel\MagickMetadataTrait;
use Tulia\Cms\Metadata\Ports\Domain\WriteModel\MetadataAwareInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\NodeId;
use Tulia\Cms\Platform\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AggregateRoot implements MetadataAwareInterface
{
    use MagickMetadataTrait;

    protected NodeId $id;

    protected string $type;

    protected string $status;

    protected string $websiteId;

    protected ImmutableDateTime $publishedAt;

    protected ?ImmutableDateTime $publishedTo = null;

    protected ImmutableDateTime $createdAt;

    protected ?ImmutableDateTime $updatedAt = null;

    protected ?string $authorId = null;

    protected ?string $parentId = null;

    protected int $level;

    protected ?string $category = null;

    protected string $locale;

    protected ?string $title = null;

    protected ?string $slug = null;

    protected ?string $introduction = null;

    protected ?string $content = null;

    protected ?string $contentCompiled = null;

    protected array $flags = [];

    protected bool $translated = true;

    private function __construct(string $id, string $type, string $websiteId, string $locale)
    {
        $this->id = new NodeId($id);
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
        $this->createdAt = new ImmutableDateTime();
    }

    public static function createNew(string $id, string $type, string $websiteId, string $locale): self
    {
        $self = new self($id, $type, $websiteId, $locale);
        $self->recordThat(new Event\NodeCreated($id, $websiteId, $locale, $type));

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
        $self->category = $data['category'] ?? null;
        $self->title = $data['title'] ?? null;
        $self->slug = $data['slug'] ?? null;
        $self->introduction = $data['introduction'] ?? null;
        $self->content = $data['content'] ?? null;
        $self->contentCompiled = $data['content_compiled'] ?? null;
        $self->translated = (bool) ($data['translated'] ?? true);
        $self->flags = $data['flags'] ?? [];
        $self->replaceMetadata($data['metadata'] ?? []);

        return $self;
    }

    public function getId(): NodeId
    {
        return $this->id;
    }

    public function setId(NodeId $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

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

    public function setWebsiteId(string $websiteId): void
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
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

    public function hasFlag(string $name): bool
    {
        return \in_array($name, $this->flags, true);
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function setFlags(array $flags): void
    {
        $this->flags = $flags;
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
