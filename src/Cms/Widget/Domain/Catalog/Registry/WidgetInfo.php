<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Catalog\Registry;

use Tulia\Cms\Widget\Domain\Catalog\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetInfo
{
    private string $id;
    private string $classname;
    private string $name;
    private string $views;
    private string $description;
    private ?string $translationDomain;
    private WidgetInterface $instance;
    private array $fields;

    public static function fromArray(array $data): self
    {
        $self = new self;
        $self->id = $data['id'];
        $self->classname = $data['classname'];
        $self->name = $data['name'];
        $self->views = $data['views'];
        $self->description = $data['description'];
        $self->translationDomain = $data['translation_domain'];
        $self->instance = $data['instance'];
        $self->fields = $data['fields'];

        return $self;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClassname(): string
    {
        return $this->classname;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getViews(): string
    {
        return $this->views;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function getInstance(): WidgetInterface
    {
        return $this->instance;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
