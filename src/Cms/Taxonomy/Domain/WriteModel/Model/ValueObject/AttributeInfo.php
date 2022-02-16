<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeInfo
{
    private bool $multilingual;
    private bool $compilable;
    private bool $hasNonscalarValue;
    private bool $taxonomy;

    public function __construct(bool $multilingual, bool $compilable, bool $hasNonscalarValue, bool $taxonomy)
    {
        $this->multilingual = $multilingual;
        $this->compilable = $compilable;
        $this->hasNonscalarValue = $hasNonscalarValue;
        $this->taxonomy = $taxonomy;
    }

    public function isMultilingual(): bool
    {
        return $this->multilingual;
    }

    public function isCompilable(): bool
    {
        return $this->compilable;
    }

    public function hasNonscalarValue(): bool
    {
        return $this->hasNonscalarValue;
    }

    public function isTaxonomy(): bool
    {
        return $this->taxonomy;
    }
}
