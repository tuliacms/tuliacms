<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Aggregate;

use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;
use Tulia\Cms\Platform\Domain\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AggregateRoot
{
    /**
     * @var AggregateId
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
     * @var ImmutableDateTime
     */
    protected $publishedAt;

    /**
     * @var null|ImmutableDateTime
     */
    protected $publishedTo;

    /**
     * @var ImmutableDateTime
     */
    protected $createdAt;

    /**
     * @var null|ImmutableDateTime
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

    /**
     * @param AggregateId $id
     * @param string $type
     * @param string $websiteId
     * @param string $locale
     */
    public function __construct(AggregateId $id, string $type, string $websiteId, string $locale)
    {
        $this->id   = $id;
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;

        $this->recordThat(new Event\NodeCreated($id, $type, $websiteId, $locale));
    }

    /**
     * @return AggregateId
     */
    public function getId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function changeMetadataValue(string $name, $value): void
    {
        if (\array_key_exists($name, $this->metadata)) {
            if ($this->metadata[$name] !== $value) {
                if (empty($value)) {
                    $this->recordThat(new Event\MetadataValueDeleted($this->id, $name, $value));
                } else {
                    $this->recordThat(new Event\MetadataValueChanged($this->id, $name, $value));
                }

                $this->metadata[$name] = $value;
            }
        } else {
            if (empty($value) === false) {
                $this->recordThat(new Event\MetadataValueChanged($this->id, $name, $value));
                $this->metadata[$name] = $value;
            }
        }
    }

    /**
     * @param string $status
     */
    public function changePublicationStatus(string $status): void
    {
        if ($this->status !== $status) {
            $this->status = $status;
            $this->recordThat(new Event\PublicationStatusChanged($this->id, $status));
        }
    }

    /**
     * @param ImmutableDateTime $from
     * @param ImmutableDateTime|null $to
     */
    public function changePublicationPeriod(ImmutableDateTime $from, ?ImmutableDateTime $to = null): void
    {
        if (
            $this->publishedAt === null
            || $this->publishedAt->sameAs($from) === false
            || (
                $this->publishedTo instanceof ImmutableDateTime
                && $to instanceof ImmutableDateTime
                && $this->publishedTo->sameAs($to)
            )
        ) {
            $this->publishedAt = $from;
            $this->publishedTo = $to;
            $this->recordThat(new Event\PublicationPeriodChanged($this->id, $from, $to));
        }
    }

    /**
     * @param string $authorId
     */
    public function assignAuthor(string $authorId): void
    {
        if ($this->authorId !== $authorId) {
            $this->authorId = $authorId;
            $this->recordThat(new Event\AuthorAssigned($this->id, $authorId));
        }
    }

    /**
     * @param null|string $category
     */
    public function categorize(?string $category): void
    {
        if ($this->category !== $category) {
            $this->category = $category;
            $this->recordThat(new Event\Categorized($this->id, $category));
        }
    }

    /**
     * @param null|string $slug
     */
    public function changeSlug(?string $slug): void
    {
        if ($this->slug !== $slug) {
            $this->slug = $slug;
            $this->recordThat(new Event\SlugChanged($this->id, $slug));
        }
    }

    /**
     * @param null|string $title
     */
    public function changeTitle(?string $title): void
    {
        if ($this->title !== $title) {
            $this->title = $title;
            $this->recordThat(new Event\TitleChanged($this->id, $title));
        }
    }

    /**
     * @param null|string $content
     */
    public function changeContent(?string $content): void
    {
        if ($this->content !== $content) {
            $this->content = $content;
            $this->recordThat(new Event\ContentChanged($this->id, $content));
        }
    }

    /**
     * @param string|null $contentSource
     */
    public function changeContentSource(?string $contentSource): void
    {
        if ($this->contentSource !== $contentSource) {
            $this->contentSource = $contentSource;
            $this->recordThat(new Event\ContentSourceChanged($this->id, $contentSource));
        }
    }

    /**
     * @param null|string $introduction
     */
    public function changeIntroduction(?string $introduction): void
    {
        if ($this->introduction !== $introduction) {
            $this->introduction = $introduction;
            $this->recordThat(new Event\IntroductionChanged($this->id, $introduction));
        }
    }

    /**
     * @param int $level
     */
    public function moveToLevel(int $level): void
    {
        if ($this->level !== $level) {
            $this->level = $level;
            $this->recordThat(new Event\MovedToLevel($this->id, $level));
        }
    }

    /**
     * @param null|string $parentId
     */
    public function assignToParent(?string $parentId): void
    {
        if ($this->parentId !== $parentId) {
            $this->parentId = $parentId;
            $this->recordThat(new Event\AssignedToParent($this->id, $parentId));
        }
    }
}
