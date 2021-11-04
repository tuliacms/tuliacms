<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeNotExistsException extends \Exception
{
    private string $type;

    public static function fromType(string $type): self
    {
        $self = new self(sprintf('Taxonomy type "%s" not exists.', $type));
        $self->type = $type;

        return $self;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
