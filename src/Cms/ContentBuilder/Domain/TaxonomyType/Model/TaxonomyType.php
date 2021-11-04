<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model;

use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Exception\MultipleValueForTitleOrSlugOccuredException;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyType
{
    private string $type;
    private string $translationDomain = 'messages';
    private string $controller = 'Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node::show';
    private bool $isRoutable = true;
    private bool $isHierarchical = false;
    private string $routingStrategy = 'simple';

    /**
     * @var Field[]
     */
    private array $fields = [];

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
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

    public function getRoutingStrategy(): string
    {
        return $this->routingStrategy;
    }

    public function setRoutingStrategy(string $routingStrategy): void
    {
        $this->routingStrategy = $routingStrategy;
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
}
