<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeNotExistsException extends \Exception
{
    private string $type;

    public static function fromType(string $type): self
    {
        $self = new self(sprintf('Node type "%s" not exists.', $type));
        $self->type = $type;

        return $self;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
