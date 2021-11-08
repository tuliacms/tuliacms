<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class MissingRoutableFieldException extends \Exception
{
    private string $fieldName;
    private string $nodeType;

    public static function fromName(string $nodeType, string $fieldName): self
    {
        $self = new self(sprintf('Cannot set "%s" field as routable for "%s" NodeType, field not exists in fields for this NodeType.', $fieldName, $nodeType));
        $self->fieldName = $fieldName;
        $self->nodeType = $nodeType;

        return $self;
    }
}
