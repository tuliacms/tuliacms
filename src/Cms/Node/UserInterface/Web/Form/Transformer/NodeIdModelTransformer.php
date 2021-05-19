<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\NodeId;

/**
 * @author Adam Banaszkiewicz
 */
class NodeIdModelTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if ($value instanceof NodeId) {
            return $value->getId();
        }

        return '';
    }

    public function reverseTransform($value): NodeId
    {
        return new NodeId($value);
    }
}
