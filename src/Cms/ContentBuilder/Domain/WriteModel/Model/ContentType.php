<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\Model;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\CannotOverwriteInternalFieldException;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;

/**
 * @author Adam Banaszkiewicz
 */
class ContentType
{
    protected string $id;
    protected string $type;
    protected ?string $controller = null;
    protected LayoutType $layout;
    protected string $code;
    protected string $name = '';
    protected string $icon = '';
    protected bool $isRoutable = false;
    protected bool $isHierarchical = false;
    protected ?string $routingStrategy = null;

    /**
     * @var Field[]
     */
    protected array $fields = [];

    public function __construct(string $id, string $code, string $type, LayoutType $layout)
    {
        $this->id = $id;
        $this->code = $code;
        $this->type = $type;
        $this->layout = $layout;
    }

    public static function recreateFromArray(array $data): self
    {
        $layout = new LayoutType($data['layout']['code']);

        foreach ($data['layout']['sections'] as $sectionCode => $section) {
            $groups = [];

            foreach ($section['groups'] as $groupName => $group) {
                $groups[$groupName] = new FieldsGroup($group['code'], $group['name'], (bool) $group['active'], (string) $group['interior'], $group['fields']);
            }

            $layout->addSection(new Section($sectionCode, $groups));
        }

        $self = new self($data['id'], $data['code'], $data['type'], $layout);
        $self->name = $data['name'];
        $self->icon = $data['icon'];
        $self->isRoutable = (bool) $data['is_routable'];
        $self->isHierarchical = (bool) $data['is_hierarchical'];
        $self->routingStrategy = $data['routing_strategy'];

        foreach ($data['fields'] as $field) {
            $self->fields[$field['code']] = new Field($field);
        }

        return $self;
    }

    public function getId(): string
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
     * @throws CannotOverwriteInternalFieldException
     */
    protected function validateField(Field $field): void
    {
        if (isset($this->fields[$field->getCode()]) && $this->fields[$field->getCode()]->isInternal()) {
            throw CannotOverwriteInternalFieldException::fromCodeAndName($field->getCode(), $field->getName());
        }
    }
}
