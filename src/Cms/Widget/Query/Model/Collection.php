<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Query\Model;

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
        if (! $element instanceof Widget) {
            throw new \InvalidArgumentException(sprintf('Element must be instance of %s', Widget::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function first(): ?Widget
    {
        return $this->elements[0] ?? null;
    }
}
