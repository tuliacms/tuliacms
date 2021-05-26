<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Type;

use Tulia\Cms\Menu\UserInterface\Web\Backend\Selector\SelectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Type implements TypeInterface
{
    protected string $type;

    protected string $label = '';

    protected string $translationDomain = '';

    protected ?SelectorInterface $selectorService = null;

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
    public function getSelectorService(): ?SelectorInterface
    {
        return $this->selectorService;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelectorService(?SelectorInterface $selectorService): void
    {
        $this->selectorService = $selectorService;
    }
}
