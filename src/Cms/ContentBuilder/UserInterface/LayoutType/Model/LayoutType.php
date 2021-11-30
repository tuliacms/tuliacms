<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutType
{
    protected string $name;
    protected string $label;
    protected string $builder;

    /**
     * @var Section[]
     */
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

    public function getBuilder(): string
    {
        return $this->builder;
    }

    public function setBuilder(string $builder): void
    {
        $this->builder = $builder;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function getSection(string $name): Section
    {
        return $this->sections[$name];
    }

    public function setSections(array $sections): void
    {
        $this->sections = $sections;
    }

    public function addSection(Section $section): void
    {
        $this->sections[$section->getName()] = $section;
    }
}
