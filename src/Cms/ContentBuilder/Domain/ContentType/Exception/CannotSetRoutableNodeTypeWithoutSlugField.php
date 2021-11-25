<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class CannotSetRoutableNodeTypeWithoutSlugField extends \Exception
{
    private string $nodeType;

    public static function fromType(string $nodeType): self
    {
        $self = new self(sprintf('Cannot set "%s" as routable, without the "slug" field.', $nodeType));
        $self->nodeType = $nodeType;

        return $self;
    }
}
