<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Model;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\CannotOverwriteInternalFieldException;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;

/**
 * @author Adam Banaszkiewicz
 */
class ContentType
{
    protected ?string $id;
    protected string $type;
    protected ?string $controller = null;
    protected LayoutType $layout;
    protected string $code;
    protected string $name;
    protected string $icon;
    protected bool $isRoutable = false;
    protected bool $isHierarchical = false;
    protected bool $isInternal = false;
    protected ?string $routingStrategy = null;

    /**
     * @var Field[]
     */
    protected array $fields = [];

    public function __construct(?string $id, string $code, string $type, LayoutType $layout, bool $isInternal)
    {
        $this->id = $id;
        $this->code = $code;
        $this->type = $type;
        $this->layout = $layout;
        $this->isInternal = $isInternal;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function setController(?string $controller): void
    {
        $this->controller = $controller;
    }

    public function isRoutable(): bool
    {
        return $this->isRoutable;
    }

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function setIsRoutable(bool $isRoutable): void
    {
        if ($isRoutable && ! $this->routingStrategy) {
            throw EmptyRoutingStrategyForRoutableContentTypeException::fromType($this->type);
        }

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
        return $this->isInternal || ! $this->id;
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
     * @throws CannotOverwriteInternalFieldException
     */
    public function addField(Field $field): Field
    {
        $this->validateField($field);

        $this->fields[$field->getCode()] = $field;

        return $field;
    }

    public function getRoutingStrategy(): ?string
    {
        return $this->routingStrategy;
    }

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function setRoutingStrategy(?string $routingStrategy): void
    {
        if ($this->isRoutable && ! $routingStrategy) {
            throw EmptyRoutingStrategyForRoutableContentTypeException::fromType($this->type);
        }

        $this->routingStrategy = $routingStrategy;
    }

    /**
     * @return string[]
     */
    public function getSubfields(string $code): array
    {
        $subfields = [];

        foreach ($this->fields as $fieldCode => $field) {
            if ($field->getParent() === $code) {
                $subfields[] = $fieldCode;
            }
        }

        return $subfields;
    }

    /**
     * @throws CannotOverwriteInternalFieldException
     */
    protected function validateField(Field $field): void
    {
        if (isset($this->fields[$field->getCode()]) && $this->fields[$field->getCode()]->isInternal()) {
            throw CannotOverwriteInternalFieldException::fromCodeAndName($field->getCode(), $field->getName());
        }
    }
}
