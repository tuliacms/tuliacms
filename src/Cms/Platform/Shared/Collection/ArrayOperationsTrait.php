<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Collection;

/**
 * @property array $elements
 *
 * @author Adam Banaszkiewicz
 */
trait ArrayOperationsTrait
{
    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->elements as $element) {
            $array[] = $element->toArray();
        }

        return $array;
    }
}
