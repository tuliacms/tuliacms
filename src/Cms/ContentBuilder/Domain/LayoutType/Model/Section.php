<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\LayoutType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Section
{
    private string $code;

    /**
     * @var FieldsGroup[]
     */
    private array $fieldsGroups = [];

    public function __construct(string $code, array $fieldsGroups = [])
    {
        $this->code = $code;
        $this->fieldsGroups = $fieldsGroups;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getFieldsGroups(): array
    {
        return $this->fieldsGroups;
    }

    public function getFieldsGroup(string $code): FieldsGroup
    {
        return $this->fieldsGroups[$code];
    }

    public function setFieldsGroups(array $fieldsGroups): void
    {
        $this->fieldsGroups = $fieldsGroups;
    }
}
