<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\Model;

use Tulia\Cms\Metadata\Domain\ReadModel\MagickMetadataTrait;
use Tulia\Cms\Taxonomy\Query\Model\Term as QueryModelTerm;

/**
 * @author Adam Banaszkiewicz
 */
class Term
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
    protected $websiteId;

    /**
     * @var null|string
     */
    protected $parentId;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $slug;

    /**
     * @var bool
     */
    protected $visibility;

    /**
     * @var bool
     */
    protected $autogeneratedLocale;

    public static function fromQueryModel(QueryModelTerm $term): self
    {
        $self = new self($term->getId(), $term->getType());
        $self->setId($term->getId());
        $self->setType($term->getType());
        $self->setWebsiteId($term->getWebsiteId());
        $self->setParentId($term->getParentId());
        $self->setLevel($term->getLevel());
        $self->setCount($term->getCount());
        $self->setLocale($term->getLocale());
        $self->setName($term->getName());
        $self->setSlug($term->getSlug());
        $self->setVisibility($term->getVisibility());
        $self->setAutogeneratedLocale($term->getAutogeneratedLocale());
        $self->replaceMetadata($term->getAllMetadata());

        return $self;
    }

    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
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

    public function getVisibility(): bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function isAutogeneratedLocale(): bool
    {
        return $this->autogeneratedLocale;
    }

    public function setAutogeneratedLocale(bool $autogeneratedLocale): void
    {
        $this->autogeneratedLocale = $autogeneratedLocale;
    }
}
