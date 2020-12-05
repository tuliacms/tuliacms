<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Model;

use Tulia\Cms\Platform\Shared\Collection\AbstractCollection;
use Tulia\Cms\Platform\Shared\Collection\ArrayOperationsTrait;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleCollection extends AbstractCollection
{
    use ArrayOperationsTrait;

    /**
     * {@inheritdoc}
     */
    protected function validateType($element): void
    {
        if (! $element instanceof Locale) {
            throw new \InvalidArgumentException(sprintf('Element must be instance of %s', Locale::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function first(): ?Locale
    {
        return $this->elements[0] ?? null;
    }
}
