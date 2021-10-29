<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class MultipleValueForTitleOrSlugOccuredException extends \Exception
{
    private string $fieldType;

    public static function fromFieldType(string $fieldType): self
    {
        $self = new self(sprintf('Cannot create mutliple value field for %s type.', $fieldType));
        $self->fieldType = $fieldType;

        return $self;
    }
}
