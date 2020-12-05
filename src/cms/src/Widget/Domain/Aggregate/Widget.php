<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Aggregate;

use Tulia\Cms\Widget\Domain\Event;
use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
class Widget extends AggregateRoot
{
    /**
     * @var AggregateId
     */
    protected $id;

    /**
     * @var string
     */
    protected $websiteId;

    /**
     * @var string
     */
    protected $widgetId;

    /**
     * @var string
     */
    protected $space;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $htmlClass;

    /**
     * @var null|string
     */
    protected $htmlId;

    /**
     * @var array
     */
    protected $styles = [];

    /**
     * @var array
     */
    protected $payload = [];

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var null|string
     */
    protected $title;

    /**
     * @var bool
     */
    protected $visibility;

    /**
     * @var array
     */
    protected $payloadLocalized = [];

    /**
     * @param AggregateId $id
     */
    public function __construct(AggregateId $id, string $widgetId, string $websiteId, string $locale)
    {
        $this->id = $id;
        $this->widgetId = $widgetId;
        $this->websiteId = $websiteId;
        $this->locale = $locale;

        $this->recordThat(new Event\WidgetCreated($id, $widgetId, $websiteId, $locale));
    }

    /**
     * @return AggregateId
     */
    public function getId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @param string $space
     */
    public function moveToSpace(string $space): void
    {
        if ($this->space !== $space) {
            $this->space = $space;

            $this->recordThat(new Event\MovedToSpace($this->id, $space));
        }
    }

    /**
     * @param string $name
     */
    public function rename(string $name): void
    {
        if ($this->name !== $name) {
            $this->name = $name;

            $this->recordThat(new Event\Renamed($this->id, $name));
        }
    }

    /**
     * @param null|string $htmlClass
     */
    public function changeHtmlClass(?string $htmlClass): void
    {
        if ($this->htmlClass !== $htmlClass) {
            $this->htmlClass = $htmlClass;

            $this->recordThat(new Event\HtmlClassChanged($this->id, $htmlClass));
        }
    }

    /**
     * @param null|string $htmlId
     */
    public function changeHtmlId(?string $htmlId): void
    {
        if ($this->htmlId !== $htmlId) {
            $this->htmlId = $htmlId;

            $this->recordThat(new Event\HtmlIdChanged($this->id, $htmlId));
        }
    }

    /**
     * @param string $style
     */
    public function applyStyle(string $style): void
    {
        if (in_array($style, $this->styles) === false) {
            $this->styles[] = $style;

            $this->recordThat(new Event\StyleWasApplied($this->id, $style));
        }
    }

    /**
     * @param string $style
     */
    public function removeStyle(string $style): void
    {
        $key = array_search($style, $this->styles);

        if ($key !== false) {
            unset($this->styles[$key]);

            $this->recordThat(new Event\StyleWasRemoved($this->id, $style));
        }
    }

    /**
     * @param array $styles
     */
    public function persistStyles(array $styles): void
    {
        $new = array_diff($styles, $this->styles);
        $old = array_diff($this->styles, $styles);

        foreach ($new as $style) {
            $this->applyStyle($style);
        }

        foreach ($old as $style) {
            $this->removeStyle($style);
        }
    }

    /**
     * @param array $payload
     */
    public function updatePayload(array $payload): void
    {
        if ($this->payload !== $payload) {
            $this->payload = $payload;

            $this->recordThat(new Event\PayloadUpdated($this->id, $payload));
        }
    }

    /**
     * @param array $payloadLocalized
     */
    public function updateLocalizedPayload(array $payloadLocalized): void
    {
        if ($this->payloadLocalized !== $payloadLocalized) {
            $this->payloadLocalized = $payloadLocalized;

            $this->recordThat(new Event\LocalizedPayloadUpdated($this->id, $payloadLocalized));
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
}
