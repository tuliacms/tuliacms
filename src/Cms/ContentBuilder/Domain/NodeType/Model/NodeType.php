<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class NodeType
{
    private string $name;
    private string $layout;
    private string $translationDomain;
    private string $controller = 'Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node::show';
    private bool $isRoutable = true;
    private bool $isHierarchical = false;
    private string $routableTaxonomyField = 'category';
    private array $fields = [];

    public function __construct(string $name, string $layout)
    {
        $this->name = $name;
        $this->layout = $layout;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    public function isRoutable(): bool
    {
        return $this->isRoutable;
    }

    public function setIsRoutable(bool $isRoutable): void
    {
        $this->isRoutable = $isRoutable;
    }

    public function isHierarchical(): bool
    {
        return $this->isHierarchical;
    }

    public function setIsHierarchical(bool $isHierarchical): void
    {
        $this->isHierarchical = $isHierarchical;
    }

    public function getRoutableTaxonomyField(): string
    {
        return $this->routableTaxonomyField;
    }

    public function setRoutableTaxonomyField(string $routableTaxonomyField): void
    {
        $this->routableTaxonomyField = $routableTaxonomyField;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function addField(Field $field): void
    {
        $this->fields[$field->getName()] = $field;
    }
}
