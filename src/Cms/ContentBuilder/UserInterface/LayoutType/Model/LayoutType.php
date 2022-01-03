<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutType
{
    protected string $code;
    protected string $name;
    protected string $builder;

    /**
     * @var Section[]
     */
    protected array $sections = [];

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
