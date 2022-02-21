<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\Website\Domain\WriteModel\ValueObject\WebsiteId;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteIdModelTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if ($value instanceof WebsiteId) {
            return $value->getValue();
        }

        return '';
    }

    public function reverseTransform($value): WebsiteId
    {
        return new WebsiteId($value);
    }
}
