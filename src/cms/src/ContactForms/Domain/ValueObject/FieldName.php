<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\ValueObject;

use Tulia\Cms\ContactForms\Domain\Exception\InvalidFieldNameException;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldName
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     * @throws InvalidFieldNameException
     */
    public function __construct(string $value)
    {
        if (! preg_match('/^[a-z0-9_]+$/i', $value)) {
            throw new InvalidFieldNameException(sprintf('Field name must be only alphanumeric with underscore, "%s" given.', $value));
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
