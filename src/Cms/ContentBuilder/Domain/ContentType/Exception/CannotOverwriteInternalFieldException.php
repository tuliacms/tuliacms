<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class CannotOverwriteInternalFieldException extends \Exception
{
    private string $fieldCode;

    public static function fromCodeNadName(string $fieldCode, string $fieldName): self
    {
        $self = new self(sprintf('Cannot overwrite the internal field "%s", named "%s".', $fieldCode, $fieldName));
        $self->fieldCode = $fieldCode;

        return $self;
    }

    public function getFieldCode(): string
    {
        return $this->fieldCode;
    }
}
