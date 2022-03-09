<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Exception;

use Tulia\Component\Importer\Schema\Field;
use Tulia\Component\Importer\Schema\ObjectDefinition;

/**
 * @author Adam Banaszkiewicz
 */
class EmptyValueOfRequiredFieldException extends \Exception
{
    public static function fromField(
        ObjectDefinition $object,
        Field $field,
        string $path
    ) {
        return new self(sprintf(
            'Value of "%s.%s" field must be a "%s", but field not exists in imported data, at path "%s".',
            $object->getName(),
            $field->getName(),
            $field->getType(),
            $path
        ));
    }
}
