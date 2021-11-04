<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeInfo
{
    private bool $multilingual;
    private bool $multiple;
    private bool $compilable;

    public function __construct(bool $multilingual, bool $multiple, bool $compilable)
    {
        $this->multilingual = $multilingual;
        $this->multiple = $multiple;
        $this->compilable = $compilable;
    }

    public function isMultilingual(): bool
    {
        return $this->multilingual;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function isCompilable(): bool
    {
        return $this->compilable;
    }
}
