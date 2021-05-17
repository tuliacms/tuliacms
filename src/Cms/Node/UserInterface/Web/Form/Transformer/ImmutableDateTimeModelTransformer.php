<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\Platform\Domain\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class ImmutableDateTimeModelTransformer implements DataTransformerInterface
{
    public function transform($value): ?\DateTime
    {
        if ($value instanceof ImmutableDateTime) {
            return \DateTime::createFromImmutable($value);
        }

        return null;
    }

    public function reverseTransform($value): ?ImmutableDateTime
    {
        if ($value instanceof \DateTime) {
            return ImmutableDateTime::createFromMutable($value);
        }

        return null;
    }
}
