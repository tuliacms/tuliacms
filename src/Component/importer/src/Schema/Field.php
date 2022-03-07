<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Schema;

/**
 * @author Adam Banaszkiewicz
 */
class Field
{
    private string $name;
    private string $type;
    private bool $required;
    /** @var mixed */
    private $defaultValue;
    private bool $collection;

    /**
     * @param mixed $defaultValue
     */
    public function __construct(
        string $name,
        string $type = 'string',
        bool $required = true,
        $defaultValue = null,
        bool $collection = false
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->defaultValue = $defaultValue;
        $this->collection = $collection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function isCollection(): bool
    {
        return $this->collection;
    }
}
