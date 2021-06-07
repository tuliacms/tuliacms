<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UserInterface\Web\Backend\Form\ModelTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\ContactForms\Domain\WriteModel\Model\ValueObject\FormId;

/**
 * @author Adam Banaszkiewicz
 */
class FormIdTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if ($value instanceof FormId) {
            return $value->getId();
        }

        return '';
    }

    public function reverseTransform($value): FormId
    {
        return new FormId($value);
    }
}
