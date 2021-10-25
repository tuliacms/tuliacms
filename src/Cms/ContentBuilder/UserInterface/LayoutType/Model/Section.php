<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Section
{
    private string $name;

    /**
     * @var FieldsGroup[]
     */
    private array $fieldsGroups = [];

    public function __construct(string $name, array $fieldsGroups = [])
    {
        $this->name = $name;
        $this->fieldsGroups = $fieldsGroups;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFieldsGroups(): array
    {
        return $this->fieldsGroups;
    }

    public function getFieldsGroup(string $name): FieldsGroup
    {
        return $this->fieldsGroups[$name];
    }

    public function setFieldsGroups(array $fieldsGroups): void
    {
        $this->fieldsGroups = $fieldsGroups;
    }
}
