<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class CannotOverwriteInternalFieldException extends \Exception
{
    private string $fieldName;

    public static function fromName(string $fieldName): self
    {
        $self = new self(sprintf('Cannot overwrite the internal field, named "%s".', $fieldName));
        $self->fieldName = $fieldName;

        return $self;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
