<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\AbstractModel;

/**
 * @author Adam Banaszkiewicz
 */
class AbstractLayoutType
{
    protected string $code;
    protected string $name;

    /**
     * @var AbstractSection[]
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

    public function getSections(): array
    {
        return $this->sections;
    }

    public function getSection(string $name): AbstractSection
    {
        return $this->sections[$name];
    }

    public function clearSections(): void
    {
        $this->sections = [];
    }

    public function addSection(AbstractSection $section): void
    {
        $this->sections[$section->getCode()] = $section;
    }
}
