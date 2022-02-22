<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeHandlerAwareDataTransformer implements DataTransformerInterface
{
    private ?Attribute $attribute = null;
    private FieldTypeHandlerInterface $handler;

    public function __construct(FieldTypeHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    // From model to form
    public function transform($value)
    {
        if ($value instanceof Attribute) {
            $this->attribute = $value;
            $value = $value->getValue();
        }

        return $this->handler->prepareValueToForm($value);
    }

    // From form to model
    public function reverseTransform($value)
    {
        if ($this->attribute) {
            $this->attribute->setValue($value);
            return $this->attribute;
        }

        return $value;
    }
}
