<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Type;

/**
 * @author Adam Banaszkiewicz
 */
class Type implements TypeInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $translationDomain = '';

    /**
     * @var null|string
     */
    protected $selectorService;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectorService(): ?string
    {
        return $this->selectorService;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelectorService(?string $selectorService): void
    {
        $this->selectorService = $selectorService;
    }
}
