<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\Model;

use DateTime;
use Tulia\Cms\Node\Query\Model\Node as QueryModelNode;

/**
 * @author Adam Banaszkiewicz
 */
class Node
{
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

    /**
     * @var array
     */
    protected $metadata = [];

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
        $self->setMetadata($node->getMetadata()->all());

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
     */
    public function __isset($name): bool
    {
        return true;
    }

    /**
     * @param string $id
     * @param string $type
     * @param string $status
     */
    public function __construct(string $id, string $type, string $status)
    {
        $this->id     = $id;
        $this->type   = $type;
        $this->status = $status;
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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @param string $websiteId
     */
    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     */
    public function setPublishedAt(DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getPublishedTo(): ?DateTime
    {
        return $this->publishedTo;
    }

    /**
     * @param DateTime|null $publishedTo
     */
    public function setPublishedTo(?DateTime $publishedTo): void
    {
        $this->publishedTo = $publishedTo;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     */
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return null|string
     */
    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    /**
     * @param null|string $authorId
     */
    public function setAuthorId(?string $authorId): void
    {
        $this->authorId = $authorId;
    }

    /**
     * @return null|string
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * @param null|string $parentId
     */
    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string|null $category
     */
    public function setCategory(?string $category): void
    {
        $this->category = $category;
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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string|null
     */
    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    /**
     * @param string|null $introduction
     */
    public function setIntroduction(?string $introduction): void
    {
        $this->introduction = $introduction;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getContentSource(): ?string
    {
        return $this->contentSource;
    }

    /**
     * @param string|null $contentSource
     */
    public function setContentSource(?string $contentSource): void
    {
        $this->contentSource = $contentSource;
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
