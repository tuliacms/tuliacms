<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Model;

use Tulia\Cms\Widget\Domain\WriteModel\Event;
use Tulia\Cms\Widget\Domain\WriteModel\Model\ValueObject\WidgetId;
use Tulia\Cms\Platform\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Component\Widget\Configuration\ArrayConfiguration;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;
use Tulia\Component\Widget\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Widget extends AggregateRoot
{
    protected WidgetId $id;

    protected string $websiteId;

    protected WidgetInterface $widgetInstance;

    protected ?string $space = null;

    protected ?string $name = null;

    protected ?string $htmlClass = null;

    protected ?string $htmlId = null;

    protected array $styles = [];

    protected array $payload = [];

    protected string $locale = 'en_US';

    protected ?string $title = null;

    protected bool $visibility;

    protected array $payloadLocalized = [];

    protected ConfigurationInterface $widgetConfiguration;

    protected bool $translated = true;

    private function __construct(string $id, WidgetInterface $widgetType, string $websiteId, string $locale)
    {
        $this->id = new WidgetId($id);
        $this->widgetInstance = $widgetType;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public static function createNew(string $id, WidgetInterface $widgetType, string $websiteId, string $locale): self
    {
        $self = new self($id, $widgetType, $websiteId,  $locale);
        $self->recordThat(Event\WidgetCreated::fromWidget($self));

        self::setConfigurationObject($self);

        return $self;
    }

    public static function buildFromArray(array $data): self
    {
        $self = new self(
            $data['id'],
            $data['widget_type'],
            $data['website_id'],
            $data['locale']
        );
        $self->space = $data['space'] ?? null;
        $self->name = $data['name'] ?? null;
        $self->htmlClass = $data['html_class'] ?? null;
        $self->htmlId = $data['html_id'] ?? null;
        $self->styles = $data['styles'] ?? [];
        $self->payload = $data['payload'] ?? [];
        $self->title = $data['title'] ?? null;
        $self->visibility = (bool) ($data['visibility'] ?? true);
        $self->payloadLocalized = $data['payload_localized'] ?? [];
        $self->translated = (bool) ($data['translated'] ?? true);

        self::setConfigurationObject($self);

        return $self;
    }

    private static function setConfigurationObject(Widget $self): void
    {
        $config = new ArrayConfiguration();
        $config->merge($self->getPayload());
        $config->merge($self->getPayloadLocalized());
        $config->multilingualFields(array_keys($self->getPayloadLocalized()));

        $self->setWidgetConfiguration($config);

        $configs = $config->all();
        $self->getWidgetInstance()->configure($config);
        $config->merge($configs);
    }

    public function getId(): WidgetId
    {
        return $this->id;
    }

    public function setId(WidgetId $id): void
    {
        $this->id = $id;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    public function getWidgetInstance(): WidgetInterface
    {
        return $this->widgetInstance;
    }

    public function getSpace(): ?string
    {
        return $this->space;
    }

    public function setSpace(?string $space): void
    {
        $this->space = $space;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getHtmlClass(): ?string
    {
        return $this->htmlClass;
    }

    public function setHtmlClass(?string $htmlClass): void
    {
        $this->htmlClass = $htmlClass;
    }

    public function getHtmlId(): ?string
    {
        return $this->htmlId;
    }

    public function setHtmlId(?string $htmlId): void
    {
        $this->htmlId = $htmlId;
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function setStyles(array $styles): void
    {
        $this->styles = $styles;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
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

    public function getVisibility(): bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getPayloadLocalized(): array
    {
        return $this->payloadLocalized;
    }

    public function setPayloadLocalized(array $payloadLocalized): void
    {
        $this->payloadLocalized = $payloadLocalized;
    }

    public function getWidgetConfiguration(): ConfigurationInterface
    {
        return $this->widgetConfiguration;
    }

    public function setWidgetConfiguration(ConfigurationInterface $widgetConfiguration): void
    {
        $this->widgetConfiguration = $widgetConfiguration;
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    public function setTranslated(bool $translated): void
    {
        $this->translated = $translated;
    }

    /*public function getId(): WidgetId
    {
        return $this->id;
    }

    public function moveToSpace(string $space): void
    {
        if ($this->space !== $space) {
            $this->space = $space;

            $this->recordThat(new Event\MovedToSpace($this->id, $space));
        }
    }

    public function rename(string $name): void
    {
        if ($this->name !== $name) {
            $this->name = $name;

            $this->recordThat(new Event\Renamed($this->id, $name));
        }
    }

    public function changeHtmlClass(?string $htmlClass): void
    {
        if ($this->htmlClass !== $htmlClass) {
            $this->htmlClass = $htmlClass;

            $this->recordThat(new Event\HtmlClassChanged($this->id, $htmlClass));
        }
    }

    public function changeHtmlId(?string $htmlId): void
    {
        if ($this->htmlId !== $htmlId) {
            $this->htmlId = $htmlId;

            $this->recordThat(new Event\HtmlIdChanged($this->id, $htmlId));
        }
    }

    public function applyStyle(string $style): void
    {
        if (in_array($style, $this->styles) === false) {
            $this->styles[] = $style;

            $this->recordThat(new Event\StyleWasApplied($this->id, $style));
        }
    }

    public function removeStyle(string $style): void
    {
        $key = array_search($style, $this->styles);

        if ($key !== false) {
            unset($this->styles[$key]);

            $this->recordThat(new Event\StyleWasRemoved($this->id, $style));
        }
    }

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

    public function updatePayload(array $payload): void
    {
        if ($this->payload !== $payload) {
            $this->payload = $payload;

            $this->recordThat(new Event\PayloadUpdated($this->id, $payload));
        }
    }

    public function updateLocalizedPayload(array $payloadLocalized): void
    {
        if ($this->payloadLocalized !== $payloadLocalized) {
            $this->payloadLocalized = $payloadLocalized;

            $this->recordThat(new Event\LocalizedPayloadUpdated($this->id, $payloadLocalized));
        }
    }

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

    public function changeTitle(?string $title): void
    {
        if ($this->title !== $title) {
            $this->title = $title;

            $this->recordThat(new Event\TitleChanged($this->id, $title));
        }
    }*/
}
