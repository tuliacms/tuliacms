<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Validation;

use Tulia\Component\Importer\Exception\InvalidFieldDataTypeException;

/**
 * @author Adam Banaszkiewicz
 */
interface SchemaValidatorInterface
{
    /**
     * @throws InvalidFieldDataTypeException
     */
    public function validate(array $data): array;
}
