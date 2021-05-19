<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Finder\Model;

use DateTime;
use InvalidArgumentException;
use Tulia\Cms\Metadata\Domain\ReadModel\MagickMetadataTrait;
use Tulia\Cms\Node\Domain\ReadModel\NodeContent\NodeContentInterface;
use Tulia\Cms\Node\Infrastructure\Domain\ReadModel\NodeContent\VoidNodeContent;

/**
 * @author Adam Banaszkiewicz
 */
class Node
{
    use MagickMetadataTrait;

    protected $id;
    protected $type;
    protected $status;
    protected $websiteId;
    protected $publishedAt;
    protected $publishedTo;
    protected $createdAt;
    protected $updatedAt;
    protected $authorId;
    protected $parentId;
    protected $level;
    protected $category;
    protected $locale;
    protected $title;
    protected $slug;
    protected $introduction;
    protected NodeContentInterface $content;
    protected $visibility;

    public function __construct()
    {
        $this->content = new VoidNodeContent();
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

        $data = static::setDatetime($data, 'published_at', new DateTime());
        $data = static::setDatetime($data, 'published_to');
        $data = static::setDatetime($data, 'created_at', new DateTime());
        $data = static::setDatetime($data, 'updated_at');

        $node->setId($data['id']);
        $node->setType($data['type'] ?? 'page');
        $node->setStatus($data['status'] ?? 'published');
        $node->setWebsiteId($data['website_id']);
        $node->setPublishedAt($data['published_at']);
        $node->setPublishedTo($data['published_to']);
        $node->setCreatedAt($data['created_at']);
        $node->setUpdatedAt($data['updated_at']);
        $node->setAuthorId($data['author_id'] ?? null);
        $node->setParentId($data['parent_id'] ?? null);
        $node->setLevel((int) ($data['level'] ?? 0));
        $node->setCategory($data['category'] ?? null);
        $node->setLocale($data['locale']);
        $node->setTitle($data['title'] ?? '');
        $node->setSlug($data['slug'] ?? '');
        $node->setIntroduction($data['introduction'] ?? '');
        $node->setContent($data['content'] ?? '');
        $node->replaceMetadata($data['metadata'] ?? []);

        return $node;
    }

    private static function setDatetime(array $data, string $key, $default = null): array
    {
        if (\array_key_exists($key, $data) === false) {
            $data[$key] = $default;
        } elseif ($data[$key] === null && $default === null) {
            // Do nothing, allow to null;
        } elseif ($data[$key] instanceof DateTime === false) {
            $data[$key] = new DateTime($data[$key]);
        }

        return $data;
    }

    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(?string $id): void
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

    public function getWebsiteId(): ?string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(?string $websiteId): void
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

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): void
    {
        $this->introduction = $introduction;
    }

    public function getContent(): NodeContentInterface
    {
        return $this->content;
    }

    /**
     * @param null|string|NodeContentInterface $content
     */
    public function setContent($content): void
    {
        if ($content instanceof NodeContentInterface) {
            $this->content = $content;
        } else {
            $this->content->setSource($content);
        }
    }
}
