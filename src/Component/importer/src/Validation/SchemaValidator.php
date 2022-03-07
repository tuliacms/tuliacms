<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Validation;

use Tulia\Component\Importer\Exception\InvalidFieldDataTypeException;
use Tulia\Component\Importer\Schema\Field;
use Tulia\Component\Importer\Schema\ObjectDefinition;
use Tulia\Component\Importer\Schema\Schema;

use Tulia\Component\Importer\Structure\ObjectData;

use function is_array;
use function is_int;
use function is_string;

/**
 * @author Adam Banaszkiewicz
 */
class SchemaValidator implements SchemaValidatorInterface
{
    private Schema $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function validate(array $data): array
    {
        foreach ($data['objects'] ?? [] as $key => $object) {
            $data['objects'][$key] = $this->validateObject($object, 'objects.'.$key);
        }

        array_filter($data['objects']);

        return $data;
    }

    /**
     * @throws InvalidFieldDataTypeException
     */
    private function validateObject(array $objectData, string $path): ?ObjectData
    {
        \assert(isset($objectData['@type']), 'Missing @type key of imported Object data at path "'.$path.'".');

        if ($this->schema->has($objectData['@type']) === false) {
            return null;
        }

        $object = $this->schema->get($objectData['@type']);

        foreach ($objectData as $key => $val) {
            if ($key[0] === '@') {
                continue;
            }

            if ($object->hasField($key) === false) {
                unset($objectData[$key]);
                continue;
            }

            $objectData[$key] = $this->validateField(
                $object,
                $object->getField($key),
                $objectData[$key],
                $path.'.'.$key
            );
        }

        return new ObjectData($objectData, $object);
    }

    /**
     * @param mixed $data
     * @return mixed
     * @throws InvalidFieldDataTypeException
     */
    private function validateField(ObjectDefinition $object, Field $field, $data, string $path)
    {
        if ($field->isRequired() === false && $this->isEmpty($data)) {
            return null;
        }

        switch ($field->getType()) {
            case 'string':
                if (is_string($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, 'string',
                    $path);
                }
                break;
            case 'integer':
                if (is_int($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, 'integer',
                    $path);
                }
                break;
            case 'scalar':
                if (is_scalar($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, 'scalar',
                    $path);
                }
                break;
            case 'number':
                if (is_numeric($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, 'number',
                    $path);
                }
                break;
            case 'one_dimension_array':
                if (is_array($data) === false) {
                    foreach ($data as $val) {
                        if (is_array($val)) {
                            throw InvalidFieldDataTypeException::fromField(
                                $object,
                                $field,
                                $data,
                                'one_dimension_array',
                                $path
                            );
                        }
                    }
                }
                break;
            default:
                if ($this->schema->has($field->getType()) === false) {
                    throw InvalidFieldDataTypeException::fromField(
                        $object,
                        $field,
                        $data,
                        'one_dimension_array',
                        $path
                    );
                }

                foreach ($data as $key => $val) {
                    $data[$key] = $this->validateObject($val, $path.'.'.$key);
                }
        }

        return $data;
    }

    private function isEmpty($data): bool
    {
        return $data === null || $data === '';
    }
}
