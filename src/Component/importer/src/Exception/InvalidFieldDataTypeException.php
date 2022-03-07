<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Exception;

use Tulia\Component\Importer\Schema\Field;
use Tulia\Component\Importer\Schema\ObjectDefinition;

/**
 * @author Adam Banaszkiewicz
 */
class InvalidFieldDataTypeException extends \Exception
{
    public static function fromField(
        ObjectDefinition $object,
        Field $field,
        $data,
        string $expectedType,
        string $path
    ) {
        return new self(sprintf(
            'Value of %s.%s field must be a %s, but given %s, at path "%s".',
            $object->getName(),
            $field->getName(),
            $expectedType,
            gettype($data),
            $path
        ));
    }
}
