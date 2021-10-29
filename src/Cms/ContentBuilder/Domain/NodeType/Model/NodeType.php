<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Model;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Exception\MultipleValueForTitleOrSlugOccuredException;

/**
 * @author Adam Banaszkiewicz
 */
class NodeType
{
    private string $type;
    private string $layout;
    private string $translationDomain = 'messages';
    private string $controller = 'Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node::show';
    private bool $isRoutable = true;
    private bool $isHierarchical = false;
    private ?string $routableTaxonomyField = null;
    private array $fields = [];

    public function __construct(string $type, string $layout)
    {
        $this->type = $type;
        $this->layout = $layout;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLayout(): string
    {
        return $this->layout;
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

    public function getRoutableTaxonomyField(): ?string
    {
        return $this->routableTaxonomyField;
    }

    public function setRoutableTaxonomyField(?string $routableTaxonomyField): void
    {
        $this->routableTaxonomyField = $routableTaxonomyField;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getField(string $name): Field
    {
        return $this->fields[$name];
    }

    /**
     * @throws MultipleValueForTitleOrSlugOccuredException
     */
    public function addField(Field $field): Field
    {
        $this->validateField($field);

        $this->fields[$field->getName()] = $field;

        return $field;
    }

    /**
     * @throws MultipleValueForTitleOrSlugOccuredException
     */
    private function validateField(Field $field): void
    {
        $this->checkMultiplenessForTitleAndSlugField($field);
    }

    /**
     * @throws MultipleValueForTitleOrSlugOccuredException
     */
    private function checkMultiplenessForTitleAndSlugField(Field $field): void
    {
        if ($field->isMultiple() && ($field->isSlug() || $field->isTitle())) {
            throw MultipleValueForTitleOrSlugOccuredException::fromFieldType(
                $field->isTitle() ? 'title' : 'slug'
            );
        }
    }
}
