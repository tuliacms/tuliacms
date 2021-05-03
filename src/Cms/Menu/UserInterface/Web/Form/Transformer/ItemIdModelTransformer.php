<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\ItemId;

/**
 * @author Adam Banaszkiewicz
 */
class ItemIdModelTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if ($value instanceof ItemId) {
            return $value->getId();
        }

        return '';
    }

    public function reverseTransform($value): ItemId
    {
        return new ItemId($value);
    }
}
