<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;

/**
 * @author Adam Banaszkiewicz
 */
class Term
{
    public const ROOT_ID = '00000000-0000-0000-0000-000000000000';
    public const ROOT_LEVEL = 0;

    protected TermId $id;
    protected Taxonomy $taxonomy;
    protected ?TermId $parentId = null;
    protected int $position = 0;
    protected int $level = 0;
    protected bool $isRoot = false;
    protected string $locale = 'en_US';
    protected ?string $title = null;
    protected ?string $slug = null;
    protected ?string $path = null;
    protected bool $visibility = true;
    protected bool $translated = false;
    protected array $attributes = [];
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

        return $self;
    }

    public static function createRoot(Taxonomy $taxonomy, string $locale): self
    {
        $item = new self(self::ROOT_ID, $taxonomy, $locale, true);
        $item->title = 'root';
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
        $self->title = $data['title'] ?? null;
        $self->slug = $data['slug'] ?? null;
        $self->path = $data['path'] ?? null;
        $self->visibility = (bool) ($data['visibility'] ?? true);
        $self->translated = (bool) ($data['translated'] ?? false);

        return $self;
    }

    public function getId(): TermId
    {
        return $this->id;
    }

    public function updateAttributes(array $attributes): void
    {
        unset(
            $attributes['id'],
            $attributes['title'],
        );

        foreach ($attributes as $name => $value) {
            if ($this->taxonomy->hasAttributeInfo($name) === false) {
                throw new \Exception(sprintf('Attribute "%s" Must have AttributeInfo for this attribute.', $name));
            }

            if (isset($this->attributes[$name]) === false || $this->attributes[$name] !== $value) {
                $this->attributes[$name] = $value;
                /**
                 * Calling recordUniqueThat() prevents the system to record multiple changes on the same attribute.
                 * This may be caused, in example, by SlugGenerator: first time system sets raw value from From,
                 * and then SlugGenerator sets the validated and normalized slug. For us, the last updated
                 * attribute's value matters, so we remove all previous events and adds new, at the end of
                 * collection.
                 */
                /*$this->recordUniqueThat(AttributeUpdated::fromNode($this, $name, $value), function ($event) use ($name) {
                    return $name === $event->getAttribute();
                });*/
            }
        }
    }

    public function getAttributes(): array
    {
        return $this->attributes;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
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

    /**
     * @param mixed $code
     */
    private function validateAttributeName($code): void
    {
        if (is_string($code) === false) {
            throw new \Exception('Must be string.');
        }
        if (strlen($code) < 2) {
            throw new \Exception('Must have more than 2 chars.');
        }
        if (! preg_match('/^([a-z0-9_]+)$/m', $code)) {
            throw new \Exception('Must contains only alphanumericals and underscores.');
        }
    }
}
