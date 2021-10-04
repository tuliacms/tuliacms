<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
class Layout
{
    protected string $name;
    protected string $label;
    protected ?string $translationDomain = null;
    protected string $builder;
    protected array $sections = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(?string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function getBuilder(): string
    {
        return $this->builder;
    }

    public function setBuilder(string $builder): void
    {
        $this->builder = $builder;
    }
}
