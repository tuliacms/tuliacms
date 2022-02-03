<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeInfo
{
    private bool $multilingual;
    private bool $compilable;
    private bool $multiple;
    private bool $taxonomy;

    public function __construct(bool $multilingual, bool $compilable, bool $multiple, bool $taxonomy)
    {
        $this->multilingual = $multilingual;
        $this->compilable = $compilable;
        $this->multiple = $multiple;
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

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function isTaxonomy(): bool
    {
        return $this->taxonomy;
    }
}
