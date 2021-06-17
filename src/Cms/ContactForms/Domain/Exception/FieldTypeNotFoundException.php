<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeNotFoundException extends \Exception
{
    public static function fromType(string $type): self
    {
        return new self(sprintf('Field type "%s" not found.', $type));
    }
}
