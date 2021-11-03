<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class FieldValue
{
    private $value;
    private bool $multiple;
    private bool $multilingual;

    /**
     * @param mixed $value
     * @param bool $multiple
     * @param bool $multilingual
     */
    public function __construct($value, bool $multiple, bool $multilingual)
    {
        $this->value = $value;
        $this->multiple = $multiple;
        $this->multilingual = $multilingual;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function isMultilingual(): bool
    {
        return $this->multilingual;
    }
}
