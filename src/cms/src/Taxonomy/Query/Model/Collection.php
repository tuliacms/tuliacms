<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query\Model;

use Tulia\Cms\Platform\Shared\Collection\AbstractCollection;
use Tulia\Cms\Platform\Shared\Collection\ArrayOperationsTrait;

/**
 * @author Adam Banaszkiewicz
 */
class Collection extends AbstractCollection
{
    use ArrayOperationsTrait;

    /**
     * {@inheritdoc}
     */
    protected function validateType($element): void
    {
        if (! $element instanceof Term) {
            throw new \InvalidArgumentException(sprintf('Element must be instance of %s', Term::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function first(): ?Term
    {
        return $this->elements[0] ?? null;
    }
}
