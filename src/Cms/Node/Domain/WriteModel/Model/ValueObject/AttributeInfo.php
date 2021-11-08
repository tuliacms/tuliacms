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
    private bool $taxonomy;

    public function __construct(bool $multilingual, bool $multiple, bool $compilable, bool $taxonomy)
    {
        $this->multilingual = $multilingual;
        $this->multiple = $multiple;
        $this->compilable = $compilable;
        $this->taxonomy = $taxonomy;
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

    public function isTaxonomy(): bool
    {
        return $this->taxonomy;
    }
}
