<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\Model;

use Tulia\Cms\Widget\Query\Model\Widget as QueryModelWidget;
use Tulia\Component\Widget\Configuration\ArrayConfiguration;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Widget
{
    protected $id;
    protected $websiteId;
    protected $widgetId;
    protected $name;
    protected $space;
    protected $title;
    protected $htmlClass;
    protected $htmlId;
    protected $styles = [];
    protected $visibility;
    protected $locale = 'en_US';
    protected $translated;
    protected $widgetConfiguration;

    /**
     * {@inheritdoc}
     */
    public static function fromQueryModel(QueryModelWidget $widget): self
    {
        $config = new ArrayConfiguration();
        $config->merge($widget->getPayload());
        $config->merge($widget->getPayloadLocalized());
        $config->multilingualFields(array_keys($widget->getPayloadLocalized()));

        $self = new self($widget->getId(), $widget->getWidgetId());

        $self->setWebsiteId($widget->getWebsiteId());
        $self->setName($widget->getName());
        $self->setTitle($widget->getTitle());
        $self->setSpace($widget->getSpace());
        $self->setHtmlClass($widget->getHtmlClass());
        $self->setHtmlId($widget->getHtmlId());
        $self->setStyles($widget->getStyles());
        $self->setLocale($widget->getLocale());
        $self->setVisibility($widget->getVisibility());
        $self->setTranslated($widget->isTranslated());
        $self->setWidgetConfiguration($config);

        return $self;
    }

    public function __construct(string $id, string $widgetId)
    {
        $this->id = $id;
        $this->widgetId = $widgetId;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getWidgetId(): string
    {
        return $this->widgetId;
    }

    /**
     * @param string $widgetId
     */
    public function setWidgetId(string $widgetId): void
    {
        $this->widgetId = $widgetId;
    }

    /**
     * @return string
     */
    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @param string $websiteId
     */
    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpace(): ?string
    {
        return $this->space;
    }

    /**
     * {@inheritdoc}
     */
    public function setSpace(?string $space): void
    {
        $this->space = $space;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlClass(): ?string
    {
        return $this->htmlClass;
    }

    /**
     * {@inheritdoc}
     */
    public function setHtmlClass(?string $htmlClass): void
    {
        $this->htmlClass = $htmlClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlId(): ?string
    {
        return $this->htmlId;
    }

    /**
     * {@inheritdoc}
     */
    public function setHtmlId(?string $htmlId): void
    {
        $this->htmlId = $htmlId;
    }

    /**
     * {@inheritdoc}
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * {@inheritdoc}
     */
    public function setStyles(array $styles): void
    {
        $this->styles = $styles;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibility(): bool
    {
        return $this->visibility;
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility($visibility): void
    {
        $this->visibility = (bool) $visibility;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getTranslated()
    {
        return $this->translated;
    }

    /**
     * @param mixed $translated
     */
    public function setTranslated($translated): void
    {
        $this->translated = $translated;
    }

    /**
     * @return ConfigurationInterface
     */
    public function getWidgetConfiguration(): ConfigurationInterface
    {
        return $this->widgetConfiguration;
    }

    /**
     * @param ConfigurationInterface $widgetConfiguration
     */
    public function setWidgetConfiguration(ConfigurationInterface $widgetConfiguration): void
    {
        $this->widgetConfiguration = $widgetConfiguration;
    }
}
