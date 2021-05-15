<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\Model;

use DateTime;
use Tulia\Cms\Metadata\Domain\WriteModel\MagickMetadataTrait;
use Tulia\Cms\Node\Query\Model\Node as QueryModelNode;

/**
 * @author Adam Banaszkiewicz
 */
class Node
{
    use MagickMetadataTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $websiteId;

    /**
     * @var DateTime
     */
    protected $publishedAt;

    /**
     * @var null|DateTime
     */
    protected $publishedTo;

    /**
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @var null|DateTime
     */
    protected $updatedAt;

    /**
     * @var null|string
     */
    protected $authorId;

    /**
     * @var null|string
     */
    protected $parentId;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var null|string
     */
    protected $category;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var null|string
     */
    protected $title;

    /**
     * @var null|string
     */
    protected $slug;

    /**
     * @var null|string
     */
    protected $introduction;

    /**
     * @var null|string
     */
    protected $content;

    /**
     * @var null|string
     */
    protected $contentSource;

    public static function fromQueryModel(QueryModelNode $node): self
    {
        $self = new self($node->getId(), $node->getType(), $node->getStatus());
        $self->setWebsiteId($node->getWebsiteId());
        $self->setPublishedAt($node->getPublishedAt());
        $self->setPublishedTo($node->getPublishedTo());
        $self->setCreatedAt($node->getCreatedAt());
        $self->setUpdatedAt($node->getUpdatedAt());
        $self->setAuthorId($node->getAuthorId());
        $self->setParentId($node->getParentId());
        $self->setLevel($node->getLevel());
        $self->setCategory($node->getCategory());
        $self->setLocale($node->getLocale());
        $self->setTitle($node->getTitle());
        $self->setSlug($node->getSlug());
        $self->setIntroduction($node->getIntroduction());
        $self->setContent($node->getContent());
        $self->setContentSource($node->getContentSource());
        $self->replaceMetadata($node->getAllMetadata());

        return $self;
    }

    public function __construct(string $id, string $type, string $status)
    {
        $this->id     = $id;
        $this->type   = $type;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
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

    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getPublishedTo(): ?DateTime
    {
        return $this->publishedTo;
    }

    public function setPublishedTo(?DateTime $publishedTo): void
    {
        $this->publishedTo = $publishedTo;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): void
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

    public function getContentSource(): ?string
    {
        return $this->contentSource;
    }

    public function setContentSource(?string $contentSource): void
    {
        $this->contentSource = $contentSource;
    }
}
