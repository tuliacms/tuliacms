<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Aggregate;

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

    /**
     * @param Locale $locale
     */
    public function replaceByCode(Locale $locale): void
    {
        foreach ($this->elements as $key => $element) {
            if ($element->getCode() === $locale->getCode()) {
                $this->elements[$key] = $locale;
            }
        }
    }
}
