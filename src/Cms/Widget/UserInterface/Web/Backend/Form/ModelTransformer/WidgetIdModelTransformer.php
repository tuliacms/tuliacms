<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Backend\Form\ModelTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\Widget\Domain\WriteModel\Model\ValueObject\WidgetId;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetIdModelTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if ($value instanceof WidgetId) {
            return $value->getValue();
        }

        return '';
    }

    public function reverseTransform($value): WidgetId
    {
        return new WidgetId($value);
    }
}
