<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder\Model;

use Tulia\Cms\Platform\Shared\Collection\AbstractCollection;
use Tulia\Cms\Platform\Shared\Collection\ArrayOperationsTrait;

/**
 * @author Adam Banaszkiewicz
 */
class ItemCollection extends AbstractCollection
{
    use ArrayOperationsTrait;

    /**
     * {@inheritdoc}
     */
    protected function validateType($element): void
    {
        if (! $element instanceof Item) {
            throw new \InvalidArgumentException(sprintf('Element must be instance of %s', Item::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function first(): ?Item
    {
        return $this->elements[0] ?? null;
    }
}
