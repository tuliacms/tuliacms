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

    public const ROOT_ID = '00000000-0000-0000-0000-000000000000';

    protected TermId $id;

    protected Taxonomy $taxonomy;

    protected ?TermId $parentId = null;

    protected int $position = 0;

    protected int $level = 0;

    protected bool $isRoot = false;

    protected string $locale = 'en_US';

    protected ?string $name = null;

    protected ?string $slug = null;

    protected ?string $path = null;

    protected bool $visibility;

    protected bool $translated = false;

    protected $changeCallback;

    private function __construct(string $id, Taxonomy $taxonomy, string $locale, bool $isRoot = false)
    {
        $this->id = new TermId($id);
        $this->taxonomy = $taxonomy;
        $this->locale = $locale;
        $this->isRoot = $isRoot;
    }

    public static function createNew(string $id, Taxonomy $taxonomy, string $locale, bool $isRoot = false): self
    {
        $self = new self($id, $taxonomy, $locale, $isRoot);
        //$self->recordThat(new Event\TermCreated($id, $type, $locale));

        return $self;
    }

    public static function createRoot(Taxonomy $taxonomy, string $locale): self
    {
        $item = new self(self::ROOT_ID, $taxonomy, $locale, true);
        $item->name = 'root';
        $item->parentId = null;
        $item->position = 0;
        $item->level = 0;
        $item->locale = $locale;
        $item->translated = false;
        $item->visibility = true;

        return $item;
    }

    public static function buildFromArray(array $data): self
    {
        $self = new self(
            $data['id'],
            $data['taxonomy'],
            $data['locale'],
            (bool) $data['is_root']
        );
        $self->parentId = $data['parent_id'] ? new TermId($data['parent_id']) : null;
        $self->level = (int) ($data['level'] ?? 0);
        $self->position = (int) ($data['position'] ?? 0);
        $self->name = $data['name'] ?? null;
        $self->slug = $data['slug'] ?? null;
        $self->path = $data['path'] ?? null;
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
        $this->recordTermChanged();
    }

    public function getParentId(): ?TermId
    {
        return $this->parentId;
    }

    public function setParentId(?TermId $parentId): void
    {
        $this->parentId = $parentId;
        $this->recordTermChanged();
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
        $this->recordTermChanged();
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
        $this->recordTermChanged();
    }

    public function isRoot(): bool
    {
        return $this->isRoot;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
        $this->recordTermChanged();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
        $this->recordTermChanged();
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
        $this->recordTermChanged();
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
        $this->recordTermChanged();
    }

    public function isVisible(): bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): void
    {
        $this->visibility = $visibility;
        $this->recordTermChanged();
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    public function setTaxonomy(Taxonomy $taxonomy, ?callable $changeCallback): void
    {
        $this->taxonomy = $taxonomy;
        $this->changeCallback = $changeCallback;
    }

    public function getTaxonomy(): Taxonomy
    {
        return $this->taxonomy;
    }

    private function recordTermChanged(): void
    {
        if ($this->changeCallback) {
            call_user_func($this->changeCallback, $this);
        }
    }
}
