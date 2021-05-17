<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Tulia\Cms\Metadata\Domain\WriteModel\MagickMetadataTrait;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;
use Tulia\Cms\Platform\Domain\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AggregateRoot
{
    use MagickMetadataTrait;

    protected AggregateId $id;

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

    /**
     * @var null|string|object
     */
    protected $content;

    protected ?string $contentSource = null;

    protected bool $translated = true;

    public function __construct(AggregateId $id, string $type, string $websiteId, string $locale)
    {
        $this->id   = $id;
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
        $this->createdAt = new \DateTimeImmutable();

        $this->recordThat(new Event\NodeCreated($id, $type, $websiteId, $locale));
    }

    public function getId(): AggregateId
    {
        return $this->id;
    }

    public function setId(AggregateId $id): void
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

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt): void
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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function getContentSource(): ?string
    {
        return $this->contentSource;
    }

    public function setContentSource(?string $contentSource): void
    {
        $this->contentSource = $contentSource;
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
