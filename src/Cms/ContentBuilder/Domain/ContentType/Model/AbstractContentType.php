<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Model;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\CannotSetRoutableNodeTypeWithoutSlugField;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\MissingRoutableFieldException;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\MultipleValueForTitleOrSlugOccuredException;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\CannotOverwriteInternalFieldException;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractContentType
{
    protected string $controller;
    protected LayoutType $layout;
    protected string $code;
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

    public function __construct(string $code, LayoutType $layout, bool $isInternal)
    {
        $this->code = $code;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLayout(): LayoutType
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

    public function getField(string $code): Field
    {
        return $this->fields[$code];
    }

    public function hasField(string $code): bool
    {
        return isset($this->fields[$code]);
    }

    /**
     * @throws MultipleValueForTitleOrSlugOccuredException
     * @throws CannotOverwriteInternalFieldException
     */
    public function addField(Field $field): Field
    {
        $this->validateField($field);

        $this->fields[$field->getCode()] = $field;

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
        if ($field->isMultiple() && in_array($field->getCode(), ['title', 'slug'])) {
            throw MultipleValueForTitleOrSlugOccuredException::fromFieldType($field->getCode());
        }

        if (isset($this->fields[$field->getCode()]) && $this->fields[$field->getCode()]->isInternal()) {
            throw CannotOverwriteInternalFieldException::fromCodeAndName($field->getCode(), $field->getName());
        }

        $this->internalValidateField($field);
    }

    /**
     * @throws CannotSetRoutableNodeTypeWithoutSlugField
     */
    protected function validateRoutableContentType(): void
    {
        if ($this->isRoutable && isset($this->fields['slug']) === false) {
            throw CannotSetRoutableNodeTypeWithoutSlugField::fromType($this->code);
        }
    }
}
