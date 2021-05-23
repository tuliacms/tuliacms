<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Model;

use DateTime;
use InvalidArgumentException;
use Tulia\Cms\Metadata\Domain\ReadModel\MagickMetadataTrait;
use Tulia\Cms\Node\Domain\ReadModel\NodeContent\NodeContentInterface;
use Tulia\Cms\Node\Infrastructure\Domain\ReadModel\NodeContent\VoidNodeContent;

/**
 * @author Adam Banaszkiewicz
 */
class Term
{
    use MagickMetadataTrait;

    protected string $id;

    protected string $type;

    protected string $websiteId;

    protected ?string $parentId = null;

    protected int $level = 1;

    protected int $count = 0;

    protected string $locale;

    protected ?string $name = null;

    protected ?string $slug = null;

    protected bool $isRoot = false;

    protected bool $visibility = true;

    public static function buildFromArray(array $data): self
    {
        $term = new self();

        if (isset($data['id']) === false) {
            throw new InvalidArgumentException('Term ID must be provided.');
        }

        if (isset($data['website_id']) === false) {
            throw new InvalidArgumentException('Term website_id must be provided.');
        }

        if (isset($data['type']) === false) {
            throw new InvalidArgumentException('Term type (taxonomy type) must be provided.');
        }

        if (isset($data['locale']) === false) {
            $data['locale'] = 'en_US';
        }

        $term->setId($data['id']);
        $term->setType($data['type']);
        $term->setWebsiteId($data['website_id']);
        $term->setParentId($data['parent_id'] ?? null);
        $term->setLevel((int) ($data['level'] ?? 0));
        $term->setCount((int) ($data['count'] ?? 0));
        $term->setLocale($data['locale']);
        $term->setName($data['name'] ?? '');
        $term->setSlug($data['slug'] ?? '');
        $term->isRoot = (bool) ($data['is_root'] ?? true);
        $term->replaceMetadata($data['metadata'] ?? []);

        return $term;
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

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
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

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function isRoot(): bool
    {
        return $this->isRoot;
    }

    public function isVisibility(): bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): void
    {
        $this->visibility = $visibility;
    }
}
