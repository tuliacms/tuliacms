<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Model;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\CannotSetRoutableNodeTypeWithoutSlugField;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\MissingRoutableFieldException;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\MultipleValueForTitleOrSlugOccuredException;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\CannotOverwriteInternalFieldException;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractContentType
{
    protected string $controller;
    protected string $layout;
    protected string $type;
    protected string $name;
    protected bool $isRoutable = true;
    protected bool $isHierarchical = false;
    protected bool $isInternal = true;
    protected string $routingStrategy = 'simple';

    /**
     * @var Field[]
     */
    protected array $fields = [];

    abstract protected function internalValidate(): void;
    abstract protected function internalValidateField(Field $field): void;

    public function __construct(string $type, string $layout, bool $isInternal)
    {
        $this->type = $type;
        $this->layout = $layout;
        $this->isInternal = $isInternal;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLayout(): string
    {
        return $this->layout;
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

    public function isInternal(): bool
    {
        return $this->isInternal;
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
     * @throws CannotOverwriteInternalFieldException
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
     * @throws CannotSetRoutableNodeTypeWithoutSlugField
     * @throws MissingRoutableFieldException
     */
    public function validate(): void
    {
        $this->validateRoutableContentType();

        $this->internalValidate();
    }

    /**
     * @throws MultipleValueForTitleOrSlugOccuredException
     * @throws CannotOverwriteInternalFieldException
     */
    protected function validateField(Field $field): void
    {
        if ($field->isMultiple() && in_array($field->getName(), ['title', 'slug'])) {
            throw MultipleValueForTitleOrSlugOccuredException::fromFieldType($field->getName());
        }

        if (isset($this->fields[$field->getName()]) && $this->fields[$field->getName()]->isInternal()) {
            throw CannotOverwriteInternalFieldException::fromName($field->getName());
        }

        $this->internalValidateField($field);
    }

    /**
     * @throws CannotSetRoutableNodeTypeWithoutSlugField
     */
    protected function validateRoutableContentType(): void
    {
        if ($this->isRoutable && isset($this->fields['slug']) === false) {
            throw CannotSetRoutableNodeTypeWithoutSlugField::fromType($this->type);
        }
    }
}
