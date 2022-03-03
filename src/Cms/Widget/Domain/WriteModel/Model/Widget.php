<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Model;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Cms\Widget\Domain\WriteModel\Event;
use Tulia\Cms\Widget\Domain\WriteModel\Model\ValueObject\WidgetId;

/**
 * @author Adam Banaszkiewicz
 */
final class Widget extends AggregateRoot
{
    protected WidgetId $id;
    protected string $websiteId;
    protected string $widgetType;
    protected ?string $space = null;
    protected ?string $name = null;
    protected ?string $htmlClass = null;
    protected ?string $htmlId = null;
    protected array $styles = [];
    protected string $locale = 'en_US';
    protected ?string $title = null;
    protected bool $visibility = true;
    protected bool $translated = true;
    /** @var Attribute[] */
    protected array $attributes = [];

    private function __construct(string $id, string $widgetType, string $websiteId, string $locale)
    {
        $this->id = new WidgetId($id);
        $this->widgetType = $widgetType;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public static function createNew(string $id, string $widgetType, string $websiteId, string $locale): self
    {
        $self = new self($id, $widgetType, $websiteId,  $locale);
        $self->recordThat(Event\WidgetCreated::fromWidget($self));

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
        $self->title = $data['title'] ?? null;
        $self->visibility = (bool) ($data['visibility'] ?? true);
        $self->translated = (bool) ($data['translated'] ?? false);
        $self->attributes = $data['attributes'];

        return $self;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()->getValue(),
            'content_type' => 'widget_' . str_replace('.', '_', $this->widgetType),
            'space' => $this->space,
            'name' => $this->name,
            'html_class' => $this->htmlClass,
            'html_id' => $this->htmlId,
            'styles' => $this->styles,
            'title' => $this->title,
            'visibility' => $this->visibility,
            'attributes' => $this->attributes,
            'widget_type' => $this->widgetType,
            'website_id' => $this->websiteId,
            'locale' => $this->locale,
        ];
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

    public function getWidgetType(): string
    {
        return $this->widgetType;
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

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param Attribute[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        unset(
            $attributes['name'],
            $attributes['space'],
            $attributes['name'],
            $attributes['html_class'],
            $attributes['html_id'],
            $attributes['styles'],
            $attributes['title'],
            $attributes['visibility'],
        );
        $this->attributes = $attributes;
    }
}
