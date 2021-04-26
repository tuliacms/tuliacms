<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UserInterface\Web\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ReceiversTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return implode(', ', $value);
    }

    public function reverseTransform($value)
    {
        return array_map('trim', explode(',', (string) $value));
    }
}
