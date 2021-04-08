<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Aggregate;

use Tulia\Cms\Platform\Shared\Collection\AbstractCollection;
use Tulia\Cms\Platform\Shared\Collection\ArrayOperationsTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldCollection extends AbstractCollection
{
    use ArrayOperationsTrait;

    /**
     * {@inheritdoc}
     */
    protected function validateType($element): void
    {
        if (! $element instanceof Field) {
            throw new \InvalidArgumentException(sprintf('Element must be instance of %s', Field::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function first(): ?Field
    {
        return $this->elements[0] ?? null;
    }
}
