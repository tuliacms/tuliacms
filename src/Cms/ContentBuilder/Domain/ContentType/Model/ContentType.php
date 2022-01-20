<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Model;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\RoutableContentTypeWithoutSlugField;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\MultipleValueForTitleOrSlugOccuredException;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\CannotOverwriteInternalFieldException;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentType
{
    protected string $type;
    protected string $controller;
    protected LayoutType $layout;
    protected string $code;
    protected string $name;
    protected string $icon;
    protected bool $isRoutable = true;
    protected bool $isHierarchical = false;
    protected bool $isInternal = true;
    protected string $routingStrategy = 'simple';

    /**
     * @var Field[]
     */
    protected array $fields = [];

    protected function internalValidate(): void
    {

    }

    protected function internalValidateField(Field $field): void
    {

    }

    public function __construct(string $code, string $type, LayoutType $layout, bool $isInternal)
    {
        $this->code = $code;
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

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string|array $type
     * @return bool
     */
    public function isType($type): bool
    {
        return in_array($this->type, (array) $type, true);
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
     * @throws RoutableContentTypeWithoutSlugField
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
     * @throws RoutableContentTypeWithoutSlugField
     */
    protected function validateRoutableContentType(): void
    {
        if ($this->isRoutable && $this->type === 'node' && isset($this->fields['slug']) === false) {
            throw RoutableContentTypeWithoutSlugField::fromType($this->code);
        }
    }
}
