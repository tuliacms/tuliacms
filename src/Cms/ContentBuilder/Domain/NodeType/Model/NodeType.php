<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Model;

use Tulia\Cms\ContentBuilder\Domain\Field\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Exception\CannotSetRoutableNodeTypeWithoutSlugField;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Exception\MissingRoutableFieldException;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Exception\MultipleValueForTitleOrSlugOccuredException;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Exception\RoutableFieldIsNotTaxonomyTypeException;

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
    private string $icon;
    private ?string $routableTaxonomyField = null;

    /**
     * @var Field[]
     */
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

    /**
     * @throws CannotSetRoutableNodeTypeWithoutSlugField
     */
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

    /**
     * @throws MissingRoutableFieldException
     * @throws RoutableFieldIsNotTaxonomyTypeException
     */
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

    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
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

    public function getRutableField(): ?Field
    {
        return $this->fields[$this->routableTaxonomyField] ?? null;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @throws RoutableFieldIsNotTaxonomyTypeException
     * @throws CannotSetRoutableNodeTypeWithoutSlugField
     * @throws MissingRoutableFieldException
     */
    public function validate(): void
    {
        if ($this->routableTaxonomyField) {
            $this->validateRoutableTaxonomy();
        }

        if ($this->isRoutable) {
            $this->validateRoutableNodeType();
        }
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
        if ($field->isMultiple() && in_array($field->getName(), ['title', 'slug'])) {
            throw MultipleValueForTitleOrSlugOccuredException::fromFieldType($field->getName());
        }
    }

    /**
     * @throws MissingRoutableFieldException
     * @throws RoutableFieldIsNotTaxonomyTypeException
     */
    private function validateRoutableTaxonomy(): void
    {
        if (isset($this->fields[$this->routableTaxonomyField]) === false) {
            throw MissingRoutableFieldException::fromName($this->type, $this->routableTaxonomyField);
        } elseif ($this->fields[$this->routableTaxonomyField]->getType() !== 'taxonomy') {
            throw RoutableFieldIsNotTaxonomyTypeException::fromName($this->type, $this->routableTaxonomyField);
        }
    }

    /**
     * @throws CannotSetRoutableNodeTypeWithoutSlugField
     */
    private function validateRoutableNodeType(): void
    {
        if ($this->isRoutable && isset($this->fields['slug']) === false) {
            throw CannotSetRoutableNodeTypeWithoutSlugField::fromType($this->type);
        }
    }
}
