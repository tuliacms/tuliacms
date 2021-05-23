<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\ModelTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;

/**
 * @author Adam Banaszkiewicz
 */
class TermIdModelTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if ($value instanceof TermId) {
            return $value->getId();
        }

        return '';
    }

    public function reverseTransform($value): ?TermId
    {
        return $value ? new TermId($value) : null;
    }
}
