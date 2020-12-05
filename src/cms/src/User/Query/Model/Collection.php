<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query\Model;

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
        if (! $element instanceof User) {
            throw new \InvalidArgumentException(sprintf('Element must be instance of %s', User::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function first(): ?User
    {
        return $this->elements[0] ?? null;
    }
}
