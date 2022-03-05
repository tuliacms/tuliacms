<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeHandlerAwareDataTransformer implements DataTransformerInterface
{
    private Field $field;
    private FieldTypeHandlerInterface $handler;

    public function __construct(Field $field, FieldTypeHandlerInterface $handler)
    {
        $this->field = $field;
        $this->handler = $handler;
    }

    /**
     * From model to form
     * @return mixed
     */
    public function transform($value)
    {
        return $this->handler->prepareValueToForm($this->field, $value);
    }

    /**
     * From form to model
     * @return mixed
     */
    public function reverseTransform($value)
    {
        return $value;
    }
}
