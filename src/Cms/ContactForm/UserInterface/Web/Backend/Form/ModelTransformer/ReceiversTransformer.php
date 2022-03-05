<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Backend\Form\ModelTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ReceiversTransformer implements DataTransformerInterface
{
    /**
     * @param array $value
     * @return string
     */
    public function transform($value)
    {
        return implode(', ', $value);
    }

    /**
     * @param string $value
     * @return array
     */
    public function reverseTransform($value)
    {
        return array_map('trim', explode(',', (string) $value));
    }
}
