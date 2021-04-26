<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Aggregate;

use Tulia\Cms\Taxonomy\Domain\Event;
use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AggregateRoot
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
    protected $websiteId;

    /**
     * @var null|string
     */
    protected $parentId;

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

        $this->recordThat(new Event\TermCreated($id, $type, $websiteId, $locale));
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
     * @param null|string $name
     */
    public function rename(?string $name): void
    {
        if ($this->name !== $name) {
            $this->name = $name;
            $this->recordThat(new Event\Renamed($this->id, $name));
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

    /**
     * @param bool $visibility
     */
    public function changeVisibility(bool $visibility): void
    {
        if ($this->visibility !== $visibility) {
            $this->visibility = $visibility;

            if ($this->visibility) {
                $this->recordThat(new Event\VisibilityTurnedOn($this->id, $visibility));
            } else {
                $this->recordThat(new Event\VisibilityTurnedOff($this->id, $visibility));
            }
        }
    }
}
