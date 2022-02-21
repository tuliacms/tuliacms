<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Backend\Form\ModelTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\Model\ValueObject\FormId;

/**
 * @author Adam Banaszkiewicz
 */
class FormIdTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if ($value instanceof FormId) {
            return $value->getValue();
        }

        return '';
    }

    public function reverseTransform($value): FormId
    {
        return new FormId($value);
    }
}
