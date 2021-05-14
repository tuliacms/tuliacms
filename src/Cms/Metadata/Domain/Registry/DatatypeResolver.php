<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\Registry;

/**
 * @author Adam Banaszkiewicz
 */
class DatatypeResolver
{
    /**
     * Resolve value fetched from the storage (database).
     */
    public function resolve($value, string $expectedType, string $name, string $ownerId)
    {
        if ($expectedType === 'string') {
            return (string) $value;
        } elseif ($expectedType === 'integer') {
            return (int) $value;
        } elseif ($expectedType === 'float') {
            return (float) $value;
        } elseif ($expectedType === 'array') {
            return \is_array($value) ? $value : json_decode($value, true);
        }

        throw new \InvalidArgumentException(sprintf('Value of %s ownered by %s has not recognized datatype.', $name, $ownerId));
    }

    /**
     * Reverse resolve the value, to store in the storage (database)
     */
    public function reverseResolve($value, string $expectedType, string $name, string $ownerId)
    {
        if ($expectedType === 'string') {
            return (string) $value;
        } elseif ($expectedType === 'integer') {
            return (int) $value;
        } elseif ($expectedType === 'float') {
            return (float) $value;
        } elseif ($expectedType === 'array') {
            if (\is_array($value)) {
                return json_encode($value);
            }

            throw new \InvalidArgumentException(sprintf('Value of %s ownered by %s must be an array.', $name, $ownerId));
        }

        throw new \InvalidArgumentException(sprintf('Value of %s ownered by %s has not recognized datatype.', $name, $ownerId));
    }
}
