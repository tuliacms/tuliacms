<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Tulia\Cms\Metadata\Domain\WriteModel\MagickMetadataTrait;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;
use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AggregateRoot
{
    use MagickMetadataTrait;

    protected TermId $id;

    protected string $type;

    protected string $websiteId;

    protected ?string $parentId = null;

    protected string $locale;

    protected int $level = 0;

    protected ?string $name = null;

    protected ?string $slug = null;

    protected bool $visibility;

    private function __construct(string $id, string $type, string $websiteId, string $locale)
    {
        $this->id = new TermId($id);
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public static function createNew(string $id, string $type, string $websiteId, string $locale): self
    {
        $self = new self($id, $type, $websiteId, $locale);
        $self->recordThat(new Event\TermCreated($id, $type, $websiteId, $locale));

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
        $self->parentId = $data['parent_id'] ?? null;
        $self->level = (int) ($data['level'] ?? 0);
        $self->name = $data['name'] ?? null;
        $self->slug = $data['slug'] ?? null;
        $self->visibility = (bool) ($data['visibility'] ?? true);
        $self->replaceMetadata($data['metadata'] ?? []);

        return $self;
    }

    public function getId(): TermId
    {
        return $this->id;
    }

    public function setId(TermId $id): void
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

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
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
}
