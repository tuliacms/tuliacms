<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Backend\Form\ModelTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormFieldOptionsTransformer implements DataTransformerInterface
{
    /**
     * @param null|array $value
     * @return null|string
     */
    public function transform($value)
    {
        return $value ? json_encode($value) : $value;
    }

    /**
     * @param null|string $value
     * @return array
     */
    public function reverseTransform($value)
    {
        return json_decode($value);
    }
}
