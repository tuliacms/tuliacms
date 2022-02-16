<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\AbstractModel;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\CannotOverwriteInternalFieldException;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;

/**
 * @author Adam Banaszkiewicz
 */
class AbstractContentType
{
    protected string $type;
    protected ?string $controller = null;
    protected string $code;
    protected string $name = '';
    protected string $icon = '';
    protected bool $isRoutable = false;
    protected bool $isHierarchical = false;
    protected ?string $routingStrategy = null;
    protected bool $isInternal = false;

    /**
     * @var AbstractField[]
     */
    protected array $fields = [];

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

    public function getLayout(): AbstractLayoutType
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

    /**
     * @return AbstractField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getField(string $code): AbstractField
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
    public function addField(AbstractField $field): AbstractField
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
     * @return AbstractField[]
     */
    public function flattenFields(): array
    {
        return $this->flattenFieldsRecursive($this->fields);
    }

    /**
     * @param AbstractField[] $fields
     * @return AbstractField[]
     */
    private function flattenFieldsRecursive(array $fields, string $prefix = ''): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field->isType('repeatable')) {
                $flatenedSubfields = $this->flattenFieldsRecursive(
                    $field->getChildren(),
                    sprintf('%s%s.', $prefix, $field->getCode())
                );

                foreach ($flatenedSubfields as $code => $subfield) {
                    $result[$code] = $subfield;
                }
            } else {
                $result[$prefix . $field->getCode()] = $field;
            }
        }

        return $result;
    }

    /**
     * @throws CannotOverwriteInternalFieldException
     */
    protected function validateField(AbstractField $field): void
    {
        if (isset($this->fields[$field->getCode()]) && $this->fields[$field->getCode()]->isInternal()) {
            throw CannotOverwriteInternalFieldException::fromCodeAndName($field->getCode(), $field->getName());
        }
    }
}
