<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\ReadModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Widget
{
    protected string $id;

    protected string $websiteId;

    protected string $widgetType;

    protected string $space;

    protected string $name;

    protected ?string $htmlClass = null;

    protected ?string $htmlId = null;

    protected array $styles = [];

    protected array $payload = [];

    protected array $payloadLocalized = [];

    protected string $locale;

    protected string $title;

    protected bool $visibility = true;

    protected bool $translated = true;

    public static function buildFromArray(array $data): self
    {
        $widget = new self();

        if (isset($data['id']) === false) {
            throw new \InvalidArgumentException('Node ID must be provided.');
        }

        if (isset($data['website_id']) === false) {
            throw new \InvalidArgumentException('Node website_id must be provided.');
        }

        if (isset($data['locale']) === false) {
            $data['locale'] = 'en_US';
        }

        $widget->setId($data['id']);
        $widget->setWebsiteId($data['website_id']);
        $widget->setWidgetType($data['widget_type'] ?? '');
        $widget->setSpace($data['space'] ?? '');
        $widget->setName($data['name'] ?? '');
        $widget->setHtmlClass($data['html_class'] ?? '');
        $widget->setHtmlId($data['html_id'] ?? '');
        $widget->setStyles($data['styles'] ?? []);
        $widget->setPayload($data['payload'] ?? []);
        $widget->setLocale($data['locale']);
        $widget->setTitle($data['title'] ?? '');
        $widget->setVisibility((bool) ($data['visibility'] ?? true));
        $widget->setPayloadLocalized($data['payload_localized'] ?? []);
        $widget->setTranslated((bool) ($data['translated'] ?? false));

        return $widget;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
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

    public function setWidgetType(string $widgetType): void
    {
        $this->widgetType = $widgetType;
    }

    public function getSpace(): string
    {
        return $this->space;
    }

    public function setSpace(string $space): void
    {
        $this->space = $space;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
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

    public function getPayloadLocalized(): array
    {
        return $this->payloadLocalized;
    }

    public function setPayloadLocalized(array $payloadLocalized): void
    {
        $this->payloadLocalized = $payloadLocalized;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isVisibility(): bool
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

    public function setTranslated(bool $translated): void
    {
        $this->translated = $translated;
    }
}
