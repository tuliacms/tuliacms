<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class RoutableContentTypeWithoutSlugField extends \Exception
{
    private string $contentType;

    public static function fromType(string $contentType): self
    {
        $self = new self(sprintf('Cannot set "%s" as routable, without the "slug" field.', $contentType));
        $self->contentType = $contentType;

        return $self;
    }
}
