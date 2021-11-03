<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeNotExistsException extends \Exception
{
    private string $name;

    public static function fromName(string $name): self
    {
        $self = new self(sprintf('Field type "%s" not exists.', $name));
        $self->name = $name;

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
