<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UserInterface\Web\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormFieldOptionsTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value ? json_encode($value) : $value;
    }

    public function reverseTransform($value)
    {
        return json_decode($value);
    }
}
